<?php

// Get ID
$ID = get_the_ID();

// Get structures
include( MAIN .'structures.php');

// Get structure info
$structure = $_POST['demo-structure'];
$x = $_POST['demo-x'];
$y = $_POST['demo-y'];
$id = $_POST['demo-id']; // Lowercase $id is for repeating structures
$cost = 50; // Flat cost of 50
$target_decrease = -$structures[$structure][4]; // Note that it's negative! the opposite of the increase.
$happy_decrease = -$structures[$structure][7];  // Same
$cult_decrease = -$structures[$structure][8];	// as
$edu_decrease = -$structures[$structure][9];	// above.

if ($x == 10) { $x = 0; }

// Get user info
global $current_user;
get_currentuserinfo();
$cash_current = get_field('cash','user_'.$current_user->ID);

// Make sure we're not bankrupting, then proceed
if (($cash_current - $cost) < 0) {
	$alert = '<p>You can&#39;t do that &mdash; you&#39;d go bankrupt!';
} else {

	// Take cash from user
	update_field('cash', $cash_current - $cost, 'user_'.$current_user->ID);
	
	// For non-repeating structures, just remove (set location back to (0,0))
	if ($structures[$structure][2] != 0) {
		update_post_meta($ID, $structure.'-x', 0);
		update_post_meta($ID, $structure.'-y', 0);
		$level = get_post_meta($ID, $structure.'-level', true) + 1;

		// Reset level to 0
		update_post_meta($ID, $structure.'-level', 0);

		// Update target population
		$target_current = get_post_meta($ID, 'target-pop', true);
		update_post_meta($ID, 'target-pop', $target_current + $level * $target_decrease);

		// Update happiness, culture, education
		$happy = get_post_meta($ID, 'happiness', true);
		update_post_meta($ID, 'happiness', floor(100 * ( ($happy + $happy_decrease) / (100 + $happy_decrease) )));
		
		$culture = get_post_meta($ID, 'culture', true);
		update_post_meta($ID, 'culture', floor(100 * ( ($culture + $cult_decrease) / (100 + $cult_decrease) )));

		$edu = get_post_meta($ID, 'education', true);
		update_post_meta($ID, 'education', floor(100 * ( ($edu + $edu_decrease) / (100 + $edu_decrease) )));

	// For repeating structures...
	} elseif ($structures[$structure][0] == true) {
		$num = get_post_meta($ID, $structure.'s', true);
		$new = $num - 1;
		$level = get_post_meta($ID, $structure.'-'.$id.'-level', true) + 1; 
			// Plus one so that meta level of 0 becomes 1, 1 becomes 2, etc. (for target pop multipliers)

		// Update total number
		update_post_meta($ID, $structure.'s', $new);

		// Remove from meta and reset level to 0 (may change based on shifting in next step)
		delete_post_meta($ID, $structure.'-'.$id.'-x');
		delete_post_meta($ID, $structure.'-'.$id.'-y');
		update_post_meta($ID, $structure.'-'.$id.'-level', 0);

		// Shift all greater than $id down by 1
		for ($i = $id; $i <= $num; $i++) {
			if ($i < $num) {
				$up = $i + 1;
				$update_x = get_post_meta($ID, $structure.'-'.$up.'-x', true);
				$update_y = get_post_meta($ID, $structure.'-'.$up.'-y', true);
				$update_level = get_post_meta($ID, $structure.'-'.$up.'-level', true);
				update_post_meta($ID, $structure.'-'.$i.'-x', $update_x);
				update_post_meta($ID, $structure.'-'.$i.'-y', $update_y);
				update_post_meta($ID, $structure.'-'.$i.'-level', $update_level);
			// Delete the highest one (since it's shifted down)
			} else {
				delete_post_meta($ID, $structure.'-'.$i.'-x');
				delete_post_meta($ID, $structure.'-'.$i.'-y');
				delete_post_meta($ID, $structure.'-'.$i.'-level');
			}
		}
		
		// Update target population
		$target_current = get_post_meta($ID, 'target-pop', true);
		update_post_meta($ID, 'target-pop', $target_current + ($level * $target_decrease));

		// Update happiness, culture, education
		$happy = get_post_meta($ID, 'happiness', true);
		update_post_meta($ID, 'happiness', floor(100 * ( ($happy + $happy_decrease) / (100 + $happy_decrease) )));
		
		$culture = get_post_meta($ID, 'culture', true);
		update_post_meta($ID, 'culture', floor(100 * ( ($culture + $cult_decrease) / (100 + $cult_decrease) )));

		$edu = get_post_meta($ID, 'education', true);
		update_post_meta($ID, 'education', floor(100 * ( ($edu + $edu_decrease) / (100 + $edu_decrease) )));

		// Update population for residential types
		if ($structure == 'neighborhood') {
			$pop = get_post_meta($ID, 'population', true);
			update_post_meta($ID, 'population', $pop - ($level * 20));
		}
	}

	// Update the activity log. The output:
	$site_url = home_url();
	$link = get_permalink();
	$city = get_the_title();
	$output = 'A '.$structures[$structure][0].' was demolished in <a href="'.$link.'">'.$city.'</a> by <a href="'.$site_url.'/user/'.$current_user->user_login.'">'.$current_user->display_name.'</a>.';

	// Query the latest activity date
	$args = array(
				'post_type' => 'activity',
				'posts_per_page' => 1
			);
	$a_query = new WP_Query($args); 
	while ($a_query->have_posts()) : 
	$a_query->the_post(); 
	
	// Central time!
	date_default_timezone_set('America/Chicago');

	// Check to see if it's the same day as most recent activity
	if (date('Ymd') == get_the_date('Ymd')) {
		$already = get_post_meta(get_the_ID(), $current_user->user_login.'-'.$city.'-demo-'.$structure, true);
		if ($already > 0) {
			$new = $already + 1;
			$output = $new.' '.$structures[$structure][1].' were demolished in <a href="'.$link.'">'.$city.'</a> by <a href="'.$site_url.'/user/'.$current_user->user_login.'">'.$current_user->display_name.'</a>.';
			delete_post_meta(get_the_ID(), 'activity', 'A '.$structures[$structure][0].' was demolished in <a href="'.$link.'">'.$city.'</a> by <a href="'.$site_url.'/user/'.$current_user->user_login.'">'.$current_user->display_name.'</a>.');
			delete_post_meta(get_the_ID(), 'activity', $already.' '.$structures[$structure][1].' were demolished in <a href="'.$link.'">'.$city.'</a> by <a href="'.$site_url.'/user/'.$current_user->user_login.'">'.$current_user->display_name.'</a>.');
			update_post_meta(get_the_ID(), $current_user->user_login.'-'.$city.'-demo-'.$structure, $new);
		} else {
			add_post_meta(get_the_ID(), $current_user->user_login.'-'.$city.'-demo-'.$structure, 1);
		}
		add_post_meta(get_the_ID(), 'activity', $output);
	// If not, add a new activity entry
	} else {
		$activity_ID = wp_insert_post(array(
			'post_type' => 'activity',
			'post_title' => date('M j, Y'),
			'post_content' => $output,
			'post_status' => 'publish',
			)
		);
		add_post_meta($activity_ID, 'activity', $output);
		add_post_meta(get_the_ID(), $current_user->user_login.'-'.$city.'-demo-'.$structure, 1);
	}
	endwhile;
	wp_reset_postdata();

}

?>