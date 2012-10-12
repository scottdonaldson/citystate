<?php 
/*
Template Name: Build
*/

// Define paths
define('MAIN', dirname(__FILE__) . '/');

if (isset($_POST['buildCity'])) {
	
	// Get user info
	global $current_user;
	get_currentuserinfo();
	$cash_current = get_field('cash','user_'.$current_user->ID);

	// Make sure we're not bankrupting, then proceed
	if (($cash_current - 1000) < 0) {
		// Redirect
		header('Location: '.home_url().'?err=bankrupt');
	} else {
		// Get info
		$title = $_POST['cityName'];
		$slug = create_slug($title);
		$x = $_POST['x'];
		$y = $_POST['y'];

		// Insert new city
		$ID = wp_insert_post(array(
				'post_author' => $current_user->ID,
				'post_name' => $slug,
				'post_title' => $title,
				'post_status' => 'publish'
			)	
		);
		// Get URL (will redirect to this later)
		$url = get_permalink($ID);

		// Set location of city based on what user
		// selected on main map, set pop. to 0
		add_post_meta($ID, 'location-x', $x);
		add_post_meta($ID, 'location-y', $y);
		add_post_meta($ID, 'population', 0);
		add_post_meta($ID, 'target-pop', 1000);

		// Set geographic characteristics
		$geo = array('nw', 'n', 'ne', 'w', 'e', 'sw', 's', 'se');

		foreach ($geo as $cardinal) {
			// Find $map_x and $map_y on the map and set value (land or water)
			if ($cardinal == 'nw') {
				$map_x = $x - 1; $map_y = $y - 1;
			} elseif ($cardinal == 'n') {
				$map_x = $x; $map_y = $y - 1;
			} elseif ($cardinal == 'ne') {
				$map_x = $x + 1; $map_y = $y - 1;
			} elseif ($cardinal == 'w') {
				$map_x = $x - 1; $map_y = $y;
			} elseif ($cardinal == 'e') {
				$map_x = $x + 1; $map_y = $y;	
			} elseif ($cardinal == 'sw') {
				$map_x = $x - 1; $map_y = $y + 1;
			} elseif ($cardinal == 's') {
				$map_x = $x; $map_y = $y + 1;	
			} elseif ($cardinal == 'se') {
				$map_x = $x + 1; $map_y = $y + 1;	
			}		

			include( MAIN .'maps/originalia.php');
			$val = $map[$map_y][$map_x - 1];
			if ($val == 0) {
				$val = 'water';
			} elseif ($val == 1) {
				$val = 'land';
			} else { $val = $val; }

			add_post_meta($ID, 'map-'.$cardinal, $val);
		}

		// Set locations of all structures to (0,0) (unbuilt)
		include 'structures.php';
		foreach ($structures as $structure=>$values) {
			include( MAIN .'structures/values.php');

			if ($values[2] != 0) {
				add_post_meta($ID, $structure.'-x', 0);
				add_post_meta($ID, $structure.'-y', 0);
			} else {
				add_post_meta($ID, $structure.'s', 0);
			}
		}

		// Takes moneyz to build a city
		update_field('cash', $cash_current - 1000, 'user_'.$current_user->ID);

		// Update the activity log. The output:
		$site_url = home_url();
		$output = '<strong>The city of <a href="'.$url.'">'.$title.'</a> was built by <a href="'.$site_url.'/user/'.$current_user->user_login.'">'.$current_user->display_name.'</a>.</strong>';

		// Check to see if it's the same day as most recent activity
		$args = array(
					'post_type' => 'activity',
					'posts_per_page' => 1
				);
		$a_query = new WP_Query($args); 
		while ($a_query->have_posts()) : 
		$a_query->the_post(); 
		
		// Central time!
		date_default_timezone_set('America/Chicago');

		if (date('Ymd') == get_the_date('Ymd')) {
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
		}
		endwhile;
		wp_reset_postdata();

		// Redirect
		header('Location: '.$url.'?visit=first');
	}
}
get_header();
get_footer();
?>