<?php

// Get ID
$ID = get_the_ID();

// Get structures
include('structures.php');

// Get structure info
$structure = $_POST['structure'];
$x = $_POST['x'];
$y = $_POST['y'];
$cost = $structures[$structure][1];
$target_increase = $structures[$structure][2];

if ($x == 10) { $x = 0; }

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
	
	// Set location for non-repeating
	if ($structures[$structure][0] == false) {
		update_post_meta($ID, $structure.'-x', $x);
		update_post_meta($ID, $structure.'-y', $y);

		// Update target population
		$target_current = get_post_meta($ID, 'target-pop', true);
		update_post_meta($ID, 'target-pop', $target_current + $target_increase);

	// Set location for repeating
	} elseif ($structures[$structure][0] == true) {
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
		if ($structure = 'neighborhood') {
			$pop = get_post_meta($ID, 'population', true);
			update_post_meta($ID, 'population', $pop+20);
		}
	}
}

?>