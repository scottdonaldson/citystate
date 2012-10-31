<?php

// Get ID
$ID = get_the_ID();

// Get structures
include( MAIN .'structures.php');

// Get structure info
$structure = $_POST['upgrade-structure'];
$x = $_POST['upgrade-x'];
$y = $_POST['upgrade-y'];
$id = $_POST['upgrade-id']; // Lowercase $id is for repeating structures

$cost = $structures[$structure][3];
$target_increase = $structures[$structure][4];
$happy_increase = $structures[$structure][7];
$cult_increase = $structures[$structure][8];
$edu_increase = $structures[$structure][9];

if ($x == 10) { $x = 0; }

// Get user info
global $current_user;
get_currentuserinfo();
$cash_current = get_field('cash', 'user_'.$current_user->ID);

// Make sure we're not bankrupting, then proceed
if (($cash_current - $cost) < 0) {
	$alert = '<p>You can&#39;t do that &mdash; you&#39;d go bankrupt!</p>';
} else {

	// Take cash from user
	update_field('cash', $cash_current - $cost, 'user_'.$current_user->ID);
	
	// For non-repeating structures, just remove (set location back to (0,0))
	if ($structures[$structure][2] != 0) {
		
		// Get current level
		$level = get_post_meta($ID, $structure.'-level', true);

		// Increase level by 1
		update_post_meta($ID, $structure.'-level', $level + 1);

		// Update target population
		$target_current = get_post_meta($ID, 'target-pop', true);
		update_post_meta($ID, 'target-pop', $target_current + $target_increase);

		// Update happiness, culture, education
		$happy = get_post_meta($ID, 'happiness', true);
		update_post_meta($ID, 'happiness', $happy + ceil($happy_increase - $happy_increase * $happy/100));
		
		$culture = get_post_meta($ID, 'culture', true);
		update_post_meta($ID, 'culture', $culture + ceil($cult_increase - $cult_increase * $culture/100));

		$edu = get_post_meta($ID, 'education', true);
		update_post_meta($ID, 'education', $edu + ceil($edu_increase - $edu_increase * $edu/100));

	// For repeating structures...
	} else {
		$num = get_post_meta($ID, $structure.'s', true);

		// Get current level
		$level = get_post_meta($ID, $structure.'-'.$id.'-level', true);

		// Increase level by 1
		update_post_meta($ID, $structure.'-'.$id.'-level', $level + 1);
		
		// Update target population
		$target_current = get_post_meta($ID, 'target-pop', true);
		update_post_meta($ID, 'target-pop', $target_current + $target_increase);

		// Update happiness, culture, education
		$happy = get_post_meta($ID, 'happiness', true);
		update_post_meta($ID, 'happiness', $happy + ceil($happy_increase - $happy_increase * $happy/100));
		
		$culture = get_post_meta($ID, 'culture', true);
		update_post_meta($ID, 'culture', $culture + ceil($cult_increase - $cult_increase * $culture/100));

		$edu = get_post_meta($ID, 'education', true);
		update_post_meta($ID, 'education', $edu + ceil($edu_increase - $edu_increase * $edu/100));

		// Update population for residential types
		if ($structure = 'neighborhood') {
			$pop = get_post_meta($ID, 'population', true);
			update_post_meta($ID, 'population', $pop + 20);
		}
	}

	// Update the activity log. The output:
	$site_url = home_url();
	$link = get_permalink();
	$city = get_the_title();
	$output = 'A '.$structures[$structure][0].' was upgraded in <a href="'.$link.'">'.$city.'</a> by <a href="'.$site_url.'/user/'.$current_user->user_login.'">'.$current_user->display_name.'</a>.';

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
		$already = get_post_meta(get_the_ID(), $current_user->user_login.'-'.$city.'-upgrade-'.$structure, true);
		if ($already > 0) {
			$new = $already + 1;
			$output = $new.' upgrades were made to '.$structures[$structure][1].' in <a href="'.$link.'">'.$city.'</a> by <a href="'.$site_url.'/user/'.$current_user->user_login.'">'.$current_user->display_name.'</a>.';
			delete_post_meta(get_the_ID(), 'activity', 'A '.$structures[$structure][0].' was upgraded in <a href="'.$link.'">'.$city.'</a> by <a href="'.$site_url.'/user/'.$current_user->user_login.'">'.$current_user->display_name.'</a>.');
			delete_post_meta(get_the_ID(), 'activity', $already.' upgrades were made to '.$structures[$structure][1].' in <a href="'.$link.'">'.$city.'</a> by <a href="'.$site_url.'/user/'.$current_user->user_login.'">'.$current_user->display_name.'</a>.');
			update_post_meta(get_the_ID(), $current_user->user_login.'-'.$city.'-upgrade-'.$structure, $new);
		} else {
			add_post_meta(get_the_ID(), $current_user->user_login.'-'.$city.'-upgrade-'.$structure, 1);
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
		add_post_meta(get_the_ID(), $current_user->user_login.'-'.$city.'-upgrade-'.$structure, 1);
	}
	endwhile;
	wp_reset_postdata();

}

?>