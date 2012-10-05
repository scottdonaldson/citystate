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
$cost = 100; // Flat cost of 100
$target_increase = $structures[$structure][2];

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
	
	// For non-repeating structures, just remove (set location back to (0,0))
	if ($structures[$structure][0] == false) {
		
		// Get current level
		$level = get_post_meta($ID, $structure.'-level', true);

		// Increase level by 1
		update_post_meta($ID, $structure.'-level', $level + 1);

		// Update target population
		$target_current = get_post_meta($ID, 'target-pop', true);
		update_post_meta($ID, 'target-pop', $target_current + $target_increase);

	// For repeating structures...
	} elseif ($structures[$structure][0] == true) {
		$num = get_post_meta($ID, $structure.'s', true);

		// Get current level
		$level = get_post_meta($ID, $structure.'-'.$id.'-level', true);

		// Increase level by 1
		update_post_meta($ID, $structure.'-'.$id.'-level', $level + 1);
		
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