<?php

// Get ID
$ID = get_the_ID();

// Get structure info
$structure = $_POST['structure'];
$repeat = $_POST['repeat'];
$x = $_POST['x'];
$y = $_POST['y'];
$cost = $_POST['structure-cost'];

// Get user info
global $current_user;
get_currentuserinfo();
$cash_current = get_field('cash','user_'.$current_user->ID);

// Make sure we're not bankrupting, then proceed
if (($cash_current - $cost) < 0) {
	$alert = '<p>You can&#39;t do that &mdash; you&#39;d go bankrupt!</p>
		  	<p>Back to <a href="'.get_bloginfo('home_url').'">main map</a>.</p>';
} else {

	// Take cash from user
	update_field('cash', $cash_current - $cost, 'user_'.$current_user->ID);
	
	// Set structure as built
	// Set location
	update_post_meta($ID, $structure.'-x', $x);
	update_post_meta($ID, $structure.'-y', $y);

	// Repeats
	$num = get_post_meta($ID, $repeat.'s', true);
	$new = $num+1;
	
	// Add location of new repeatable
	add_post_meta($ID, $repeat.'-'.$new.'-x', $x);
	add_post_meta($ID, $repeat.'-'.$new.'-y', $y);

	// Change total number of repeatables
	update_post_meta($ID, $repeat.'s', $new);

	// Update population
	if ($repeat = 'neighborhood') {
		$pop = get_post_meta($ID, 'population', true);
		update_post_meta($ID, 'population', $pop+20);
	}
}

?>