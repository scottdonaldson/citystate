<?php

$milestones = array( 5, 10, 20, 50, 75, 100, 200, 500, 1000 );

foreach ($milestones as $milestone) {
	if ($pop < 1000*$milestone && $newpop >= 1000*$milestone) {

		// Update the activity log. The output:
		$site_url = home_url();
		$link = get_permalink();
		$city = get_the_title();
		$login = get_the_author_meta('user_login');
		$name = get_the_author_meta('display_name');
		$output = '<strong>The city of <a href="'.$link.'">'.$city.'</a>, governed by <a href="'.$site_url.'/user/'.$login.'">'.$name.'</a>, just passed '.th(1000*$milestone).' citizens.</strong>';

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
	}
}


?>