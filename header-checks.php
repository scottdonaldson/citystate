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
			$city_happy = get_post_meta($ID, 'happiness', true);
			$target = get_post_meta($ID, 'target-pop', true);
			
			// If target population is greater than population,
			// happiness helps it grow quicker
			if ($target >= $pop) {
				$newpop = $pop + ceil((0.00333 * $city_happy) * ($target - $pop));
				update_post_meta($ID, 'population', $newpop);
			// If target population is lower than population,
			// happiness slows down population loss
			} else {
				$newpop = $pop + floor((0.00333 * (100 - $city_happy)) * ($target - $pop));
				update_post_meta($ID, 'population', $newpop);
			}

			// Population milestones
			include ( MAIN . 'update/pop-milestones.php');
				
			// Taxes
			$cash = get_field('cash', 'user_'.$user_ID);
			$taxes = ceil(0.05*$pop);
			update_field('cash', $cash + $taxes, 'user_'.$user_ID);

			// Structure-related
			include ( MAIN . 'structures.php' );
			foreach ($structures as $structure=>$values) {
				include( MAIN .'structures/values.php');

				// Non-repeating structures
				if ($max == 1) {
					// Assign some variables.
					// $y tells us the y position. If the structure hasn't been built, it's 0.
					// $cost is originally the cost of construction. Multiplying it by 0.02,
					//     it will here be used as our base upkeep costs.
					// $funding is the funding for the structure. If it hasn't been set, it's
					//     equal to the previously defined $cost
					$y = get_post_meta($ID, $structure.'-y', true);
					$cost = 0.02*$cost;
					$funding = get_post_meta($ID, 'funding-'.$structure, true) > 0 ? get_post_meta($ID, 'funding-'.$structure, true) : $cost;

					// If the structure has been built, we subtract upkeep costs/funding
					if ($y != 0) {
						$cash = get_field('cash','user_'.$user_ID);
						update_field('cash', $cash - $funding, 'user_'.$user_ID);
					}

					// If the population of the city is already at or above the point 
					// where the structure is desired, certain things start to happen.
					if ($pop >= $desired) {
						$city_happy = get_post_meta($ID, 'happiness', true);

						// If the structure hasn't been built, we reduce happiness by 5%
						if ($y == 0) {				
							update_post_meta($ID, 'happiness', floor(0.95 * $city_happy));
						
						// If the structure has been built...
						} else {
							
							// For each 1000 people in the population greater than the 
							// desired population, base costs needed to keep the peace are 
							// increased by 10% (rounded to nearest 1)
							$need_funding = round($cost * (1 + 0.1 * (($pop - $desired) / 1000) ));

							// $diff is the percentage supplied vs. what is needed
							$diff = $funding / $need_funding;

							$city_happy = get_post_meta($ID, 'happiness', true);
							$city_culture = get_post_meta($ID, 'culture', true);
							$city_edu = get_post_meta($ID, 'education', true);

							// If the funding supplied for this structure is less than required,
							// people get unhappy, less cultural, and stupider.
							if ($diff < 1) {
								update_post_meta($ID, $structure.'-funding', 'bad');
								
								// To determine the reduction:
								// Take original city happy/culture/edu. 
								// Take away 1/2 of original structural increase.
								// Take weighted average of $diff and 1 (i.e. 70% -> 90%) percentage of that value.
								update_post_meta($ID, 'happiness', round($city_happy * (1 - 0.005*$happy) * (2 + $diff)/3), 3);
								update_post_meta($ID, 'culture', round($city_culture * (1 - 0.005*$culture) * (2 + $diff)/3), 3);
								update_post_meta($ID, 'education', round($city_edu * (1 - 0.005*$edu) * (2 + $diff)/3), 3);

							// Between 100% and 150%, funding is fair and no values change
							} elseif ($diff >= 1 && $diff < 1.5) {
								update_post_meta($ID, $structure.'-funding', 'fair');

							// Between 150% and 300%, funding is good and values change
							} elseif ($diff >= 1.5) {
								update_post_meta($ID, $structure.'-funding', 'good');

								// Increase is similar to building a structure but factoring in
								// our new friend $diff
								update_post_meta($ID, 'happiness', $city_happy + round(0.04 * $happy * ($diff - 1.5) * (1 - 0.01*$city_happy), 3));
								update_post_meta($ID, 'culture', $city_culture + round(0.04 * $culture * ($diff - 1.5) * (1 - 0.01*$city_culture), 3));
								update_post_meta($ID, 'education', $city_edu + round(0.04 * $edu * ($diff - 1.5) * (1 - 0.01*$city_edu), 3));
							} 
							// Above 500%, funding is EXCELLENT
							if ($diff >= 5) {
								update_post_meta($ID, $structure.'-funding', 'excellent');
							}
						
						} // end structure has been built
					
					} // end is current pop greater than desired pop

				// Repeating structures (just park and port for now)
				} elseif ( $structure == 'park' || $structure == 'port' ) {
					
					// $count tells us how many have been built.
					// $cost is originally the cost of construction. Multiplying it by 0.02,
					//     it will here be used as our base upkeep costs.
					// Here, funding is for ALL the structures together (or, if it hasn't been set, it's the 
					//     base upkeep times the number of structures)
					$count = get_post_meta($ID, $structure.'s', true);
					$cost = 0.02*$cost;
					$funding = get_post_meta($ID, 'funding-'.$structure, true) > 0 ? get_post_meta($ID, 'funding-'.$structure, true) : $cost*$count;

					// If the structure has been built, we subtract upkeep costs/funding
					if ($count > 0) {
						$cash = get_field('cash', 'user_'.$user_ID);
						update_field('cash', $cash - $funding, 'user_'.$user_ID);

						// If the population of the city is already at or above the point 
						// where the structure is desired, certain things start to happen.
						if ($pop >= $desired) {

							// For each 1000 people in the population greater than the 
							// desired population, base costs needed to keep the peace are 
							// increased by 10% (rounded to nearest 1)
							$need_funding = $count * round($cost * (1 + 0.1 * (($pop - $desired) / 1000) ));

							// $diff is the percentage supplied vs. what is needed
							$diff = $funding / $need_funding;

							$city_happy = get_post_meta($ID, 'happiness', true);
							$city_culture = get_post_meta($ID, 'culture', true);
							$city_edu = get_post_meta($ID, 'education', true);

							// If the funding supplied for this structure is less than required,
							// people get unhappy, less cultural, and stupider.
							if ($diff < 1) {
								update_post_meta($ID, $structure.'-funding', 'bad');
								
								// To determine the reduction:
								// Take original city happy/culture/edu. 
								// Take away 1/2 of original structural increase.
								// Take weighted average of $diff and 1 (i.e. 70% -> 90%) percentage of that value.
								update_post_meta($ID, 'happiness', round($city_happy * (1 - 0.005*$count*$happy) * (2 + $diff)/3), 3);
								update_post_meta($ID, 'culture', round($city_culture * (1 - 0.005*$count*$culture) * (2 + $diff)/3), 3);
								update_post_meta($ID, 'education', round($city_edu * (1 - 0.005*$count*$edu) * (2 + $diff)/3), 3);

							// Between 100% and 150%, funding is fair and no values change
							} elseif ($diff >= 1 && $diff < 1.5) {
								update_post_meta($ID, $structure.'-funding', 'fair');

							// Between 150% and 300%, funding is good and values change
							} elseif ($diff >= 1.5) {
								update_post_meta($ID, $structure.'-funding', 'good');

								// Increase is similar to building a structure but factoring in
								// our new friend $diff
								update_post_meta($ID, 'happiness', $city_happy + $count*round(0.04 * $happy * ($diff - 1.5) * (1 - 0.01*$city_happy), 3));
								update_post_meta($ID, 'culture', $city_culture + $count*round(0.04 * $culture * ($diff - 1.5) * (1 - 0.01*$city_culture), 3));
								update_post_meta($ID, 'education', $city_edu + $count*round(0.04 * $edu * ($diff - 1.5) * (1 - 0.01*$city_edu), 3));
							} 
							// Above 500%, funding is EXCELLENT
							if ($diff >= 5) {
								update_post_meta($ID, $structure.'-funding', 'excellent');
							}
						
						} // end is current pop greater than desired pop

					} // end is $count greater than 0

				} // End non-repeating or repeating structure

			} // end structure array foreach

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
 *	BANKRUPT
 */
// About to go bankrupt
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