<?php

/* 
 *	DAILY UPDATE
 */
if (isset($_POST['pass'])) {
	$pass = $_POST['pass'];
	if (md5($pass) == 'd7bd30db1b34953089b9e33e5a2d4b3b') {
		
		// Reset all users' turns to 10
		$users = get_users();
		foreach ($users as $user) {
			update_field('turns', 10, 'user_'.$user->ID);
		}

		// Update all cities
		query_posts('posts_per_page=-1');
		while (have_posts()) : the_post();
		
			// Get info
			$ID = get_the_ID();
			$user_ID = get_the_author_meta('ID');

			// Resets
			include ( MAIN . 'update/resets.php');
					
			// Update population
			$pop = get_post_meta($ID, 'population', true);
			$happy = get_post_meta($ID, 'happiness', true);
			$target = get_post_meta($ID, 'target-pop', true);
			
			// If target population is greater than population,
			// happiness helps it grow quicker
			if ($target >= $pop) {
				$newpop = $pop + ceil((.004 * $happy) * ($target - $pop));
				update_post_meta($ID, 'population', $newpop);
			// If target population is lower than population,
			// happiness slows down population loss
			} else {
				$newpop = $pop + floor((.004 * (100 - $happy)) * ($target - $pop));
				update_post_meta($ID, 'population', $newpop);
			}

			// Population milestones
			include ( MAIN . 'update/pop-milestones.php');
				
			// Taxes
			$cash = get_field('cash','user_'.$user_ID);
			$taxes = floor(0.05*$pop);
			update_field('cash', $cash+$taxes, 'user_'.$user_ID);

			// Structure-related
			include ( MAIN . 'structures.php' );
			foreach ($structures as $structure=>$values) {
				include( MAIN .'structures/values.php');

				// At this point, only run for non-repeating structures
				if ($max != 0) {
					// Make sure structure has been built, then continue
					$loc_x_[$structure] = get_post_meta($ID, $structure.'-x');
					$loc_y_[$structure] = get_post_meta($ID, $structure.'-y');

					if ( !($loc_x_[$structure][0] == 0 && $loc_y_[$structure][0] == 0) ) {
										
						// Upkeep costs (.02*cost)	
						$cash = get_field('cash','user_'.$user_ID);
						update_field('cash', $cash-(0.02 * $cost), 'user_'.$user_ID);

					}

					// Happiness is reduced by 10% if the structure is not in the city
					// and the population has crossed the point for desiring it
					if ($newpop >= $desired) {
						$happiness = get_post_meta($ID, 'happiness', true);

						if ( $loc_x_[$structure][0] == 0 && $loc_y_[$structure][0] == 0 ) {
										
							update_post_meta($ID, 'happiness', floor(.9*$happiness));

						}
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
 *	STRUCTURES (build, demolish, upgrade)
 */
if (isset($_POST['update'])) { 
	if (is_single()) {
		$type = $_GET['structure'];
		if ( $type == 'build') {
			include( MAIN . 'structures/build.php');
		} elseif ($type == 'demolish') {
			include( MAIN . 'structures/demolish.php');
		} elseif ($type == 'upgrade') {
			include( MAIN . 'structures/upgrade.php');
		}
	}
} 

/*
 *	BAD LOGIN
 */
if ($_GET['login'] == 'failed') {
	$alert = '<p>Bad login. Check your username or password and try again.</p><p><a href="'.home_url().'/wp-login.php?action=lostpassword">Did you forget your password?</a></p>';
}

/*
 *	ERRORS
 */
// Bankrupt
if ($_GET['err'] == 'bankrupt') {
	$alert = '<p>You can&#39;t do that &mdash; you&#39;d go bankrupt!</p>';
}

/*
 *	UPDATING PROFILE
 */
if ($_GET['profile'] == 'updated') { 
	$user = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
	if (isset($_POST['color'])) {
		update_field('color', $_POST['color'], 'user_'.$user->ID);
	}
	if (isset($_POST['displayName']) && $_POST['displayName'] !== '') {
		$name = $_POST['displayName'];
		wp_update_user(array('ID'=>$user->ID, 'display_name'=>$name));
		update_field('name_change', 1, 'user_'.$user->ID);
	}
	if (isset($_POST['pass1']) && isset($_POST['pass2']) && !empty($_POST['pass1']) && $_POST['pass1'] == $_POST['pass2']) {
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
	header('Location: '.home_url().'/user/'.$user->user_login);
	$alert = '<p>Profile updated. Keep on keepin&apos; on.</p>';
}
?>