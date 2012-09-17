<?php

/* 
 *	DAILY UPDATE
 */
if (isset($_POST['pass'])) {
	$pass = $_POST['pass'];
	if (md5($pass) == 'd7bd30db1b34953089b9e33e5a2d4b3b') {
		// Update all cities
		query_posts('posts_per_page=-1');
		while (have_posts()) : the_post();
		
		// Get info
		$ID = get_the_ID();
		$user_ID = get_the_author_meta('ID');
				
		// Update population
		$pop = get_field('population',$ID);
		update_field('population',floor($pop*1.1),$ID);
			
		// Taxes
		$cash = get_field('cash','user_'.$user_ID);
		$taxes = floor(0.05*$pop);
		update_field('cash', $cash+$taxes, 'user_'.$user_ID);

		// Structure-related
		include 'structures.php';
		foreach ($structures as $structure=>$values) {
			$values[] = $values;
			// At this point, only run for non-repeating structures
			if ($values[0] == false) {
				// Make sure structure has been built, then continue
				$loc_x_[$structure] = get_post_meta($ID, $structure.'-x');
				$loc_y_[$structure] = get_post_meta($ID, $structure.'-y');

				if ( !($loc_x_[$structure][0] == 0 && $loc_y_[$structure][0] == 0) ) {
									
					// Upkeep costs (.02*cost)	
					$cash = get_field('cash','user_'.$user_ID);
					update_field('cash', $cash-(0.02*$values[1]), 'user_'.$user_ID);

					// Each adds to population (.01*cost)
					$pop = get_field('population', $ID);
					update_field('population', $pop+.01*$cost, $ID);
				}
			}
		}
		endwhile;
		wp_reset_query();
		$alert = '<p>Daily update complete.</p>';
	} else {
		$alert = '<p>Bad password. Best try again.</p>';
	}
}

/*
 *	BUILDING STRUCTURES
 */
if (isset($_POST['update'])) { 
	if (is_single()) {
		include('build.php');
	}
} 

/*
 *	BAD LOGIN
 */
if ($_GET['login'] == 'failed') {
	$alert = '<p>Bad login. Check your username or password and try again.</p>';
}

/*
 *	UPDATING PROFILE
 */
if ($_GET['profile'] == 'updated') { 
	if (isset($_POST['pass1']) && isset($_POST['pass2']) && !empty($_POST['pass1']) && $_POST['pass1'] == $_POST['pass2']) {
		$user = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
	    $update = $wpdb->query($wpdb->prepare("UPDATE {$wpdb->users} SET `user_pass` = %s WHERE `ID` = %d", array(wp_hash_password($_POST['pass1']), $user_ID)));
	    if (!is_wp_error($update)) {
	        wp_cache_delete($user_ID, 'users');
	        wp_cache_delete($user->user_login, 'userlogins');
	        wp_logout();
            wp_signon(array('user_login' => $user->user_login,
                           'user_password' => $_POST['pass1']));
            ob_start();
	    }
	} 
	if (isset($_POST['color'])) {
		$user = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
		update_field('color', $_POST['color'], 'user_'.$user->ID);
	}
	$alert = '<p>Profile updated. Keep on keepin&apos; on.</p>';
}
?>