<?php 
/*
Template Name: Build
*/
if (isset($_POST['buildCity'])) {
	
	// Get user info
	global $current_user;
	get_currentuserinfo();
	$cash_current = get_field('cash','user_'.$current_user->ID);

	// Make sure we're not bankrupting, then proceed
	if (($cash_current - 300) < 0) {
		echo '<div id="alert">
		  		<p>You can&#39;t do that &mdash; you&#39;d go bankrupt!</p>
		  		<p>Back to <a href="'.bloginfo('home_url').'">main map</a>.</p>
		  	  </div>';
	} else {
		// Get info
		$title = $_POST['cityName'];
		$slug = create_slug($title);
		$x = $_POST['x'];
		$y = $_POST['y'];

		if ($x == 10) { $x = 0; }

		// Insert new city
		$ID = wp_insert_post(array(
				'post_author' => $current_user->ID,
				'post_name' => $slug,
				'post_title' => $title,
				'post_status' => 'publish'
			)	
		);
		// Set location of city based on what user
		// selected on main map, set pop. to 0
		update_field('location-x', $x, $ID);
		update_field('location-y', $y, $ID);
		update_field('population', 0, $ID);
		add_post_meta($ID, 'target-pop', 1000);

		// Set locations of all structures to (0,0) (unbuilt)
		include 'structures.php';
		foreach ($structures as $structure=>$values) {
			$values[] = $values;
			if ($values[0] == false) {
				add_post_meta($ID, $structure.'-x', 0);
				add_post_meta($ID, $structure.'-y', 0);
			} elseif ($values[0] == true) {
				add_post_meta($ID, $structure.'s', 0);
			}
		}

		// Takes moneyz to build a city
		update_field('cash',$cash_current-300,'user_'.$current_user->ID);

		// Redirect
		$url = get_permalink($ID);
		header('Location: '.$url.'?visit=first');
	}
}
get_header();
get_footer();
?>