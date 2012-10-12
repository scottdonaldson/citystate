<?php

// Get ID
$ID = get_the_ID();

// Get structures
include( MAIN .'structures.php');

// Get structure info
$structure = $_POST['build-structure'];
$x = $_POST['build-x'];
$y = $_POST['build-y'];
$cost = $structures[$structure][3];
$target_increase = $structures[$structure][4];

if ($x == 10) { $x = 0; }

// Get user info
global $current_user;
get_currentuserinfo();
$cash_current = get_field('cash', 'user_'.$current_user->ID);

// Make sure we're not bankrupting, then proceed
if (($cash_current - $cost) < 0) {
	$alert = '<p>You can&#39;t do that &mdash; you&#39;d go bankrupt!</p>
		  	<p>Back to <a href="'.get_bloginfo('home_url').'">main map</a>.</p>';
} else {

	// Take cash from user
	update_field('cash', $cash_current - $cost, 'user_'.$current_user->ID);
	
	// Set location for non-repeating
	if ($structures[$structure][2] != 0) {
		update_post_meta($ID, $structure.'-x', $x);
		update_post_meta($ID, $structure.'-y', $y);

		// Update target population
		$target_current = get_post_meta($ID, 'target-pop', true);
		update_post_meta($ID, 'target-pop', $target_current + $target_increase);

	// Set location for repeating
	} else {
		$num = get_post_meta($ID, $structure.'s', true);
		$new = $num+1;
		
		// Add location of new structure
		add_post_meta($ID, $structure.'-'.$new.'-x', $x);
		add_post_meta($ID, $structure.'-'.$new.'-y', $y);

		// Update total number
		update_post_meta($ID, $structure.'s', $new);

		// Update target population
		$target_current = get_post_meta($ID, 'target-pop', true);
		update_post_meta($ID, 'target-pop', $target_current + $target_increase);

		// Update population for residential types
		if ($structure == 'neighborhood') {
			$pop = get_post_meta($ID, 'population', true);
			update_post_meta($ID, 'population', $pop + 20);
		}
	}

	// Update the activity log. The output:
	$site_url = home_url();
	$link = get_permalink();
	$city = get_the_title();
	$output = 'A '.$structures[$structure][0].' was built in <a href="'.$link.'">'.$city.'</a> by <a href="'.$site_url.'/user/'.$current_user->user_login.'">'.$current_user->display_name.'</a>.';

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
		$already = get_post_meta(get_the_ID(), $current_user->user_login.'-'.$city.'-build-'.$structure, true);
		if ($already > 0) {
			$new = $already + 1;
			$output = $new.' '.$structures[$structure][1].' were built in <a href="'.$link.'">'.$city.'</a> by <a href="'.$site_url.'/user/'.$current_user->user_login.'">'.$current_user->display_name.'</a>.';
			delete_post_meta(get_the_ID(), 'activity', 'A '.$structures[$structure][0].' was built in <a href="'.$link.'">'.$city.'</a> by <a href="'.$site_url.'/user/'.$current_user->user_login.'">'.$current_user->display_name.'</a>.');
			delete_post_meta(get_the_ID(), 'activity', $already.' '.$structures[$structure][1].' were built in <a href="'.$link.'">'.$city.'</a> by <a href="'.$site_url.'/user/'.$current_user->user_login.'">'.$current_user->display_name.'</a>.');
			update_post_meta(get_the_ID(), $current_user->user_login.'-'.$city.'-build-'.$structure, $new);
		} else {
			add_post_meta(get_the_ID(), $current_user->user_login.'-'.$city.'-build-'.$structure, 1);
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
		add_post_meta(get_the_ID(), $current_user->user_login.'-'.$city.'-build-'.$structure, 1);
	}
	endwhile;
	wp_reset_postdata();

}

?>