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
	$cities = count_user_posts($current_user->ID);

	// Make sure we're not bankrupting, then proceed
	if (($cash_current - (1500 * $cities + 500)) < 0) {
		// Redirect
		header('Location: '.home_url().'?err=bankrupt');
	} else {
		// Get info
		$title = $_POST['cityName'];
		$slug = create_slug($title);
		$region_id = $_POST['region_id'];
		$region_slug = $_POST['region_slug'];
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
		// Set region
		wp_set_post_terms($ID, $region_id, 'category');

		// Get URL (will redirect to this later)
		$url = get_permalink($ID);

		// Set location of city based on what user
		// selected on main map, set pop. to 0
		add_post_meta($ID, 'location-x', $x);
		add_post_meta($ID, 'location-y', $y);
		add_post_meta($ID, 'population', 0);
		add_post_meta($ID, 'target-pop', 1000);
		add_post_meta($ID, 'happiness', 50);

		// Set natural resources
		include ( MAIN .'maps/'.$region_slug.'.php');
		$resources = $map[$y][$x - 1][1];
		foreach ($resources as $resource=>$value) {
			add_post_meta($ID, $resource, $value);
		}

		// Set geographic characteristics...
		include ( MAIN . 'build/geo.php');

		// Set locations of all structures to (0,0) (unbuilt)
		include ( MAIN . 'structures.php');
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
		update_field('cash', $cash_current - (1500*$cities + 500), 'user_'.$current_user->ID);

		// Update the activity log. The output:
		$site_url = home_url();
		$region = get_the_category($ID);
		$output = '<strong>The city of <a href="'.$url.'">'.$title.'</a> was built in '.$region[0]->cat_name.' by <a href="'.$site_url.'/user/'.$current_user->user_login.'">'.$current_user->display_name.'</a>.</strong>';

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