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
$target_decrease = -$structures[$structure][2]; // Note that it's negative! the opposite of the increase

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
	
	// For non-repeating structures, just remove (set location back to (0,0))
	if ($structures[$structure][0] == false) {
		update_post_meta($ID, $structure.'-x', 0);
		update_post_meta($ID, $structure.'-y', 0);
		$level = get_post_meta($ID, $structure.'-level', 0) + 1;

		// Reset level to 0
		update_post_meta($ID, $structure.'-level', 0);

		// Update target population
		$target_current = get_post_meta($ID, 'target-pop', true);
		update_post_meta($ID, 'target-pop', $target_current + $level * $target_decrease);

	// For repeating structures...
	} elseif ($structures[$structure][0] == true) {
		$num = get_post_meta($ID, $structure.'s', true);
		$new = $num-1;
		$level = get_post_meta($ID, $structure.'-'.$id.'-level', true) + 1; 
			// Plus one so that meta level of 0 becomes 1, 1 becomes 2, etc. (for target pop multipliers)

		// Update total number
		update_post_meta($ID, $structure.'s', $new);

		// Remove from meta
		delete_post_meta($ID, $structure.'-'.$id.'-x');
		delete_post_meta($ID, $structure.'-'.$id.'-y');

		// Shift all greater than $id down by 1
		for ($i = $id; $i <= $num; $i++) {
			if ($i != $num) {
				$up = $i+1;
				$update_x = get_post_meta($ID, $structure.'-'.$up.'-x', true);
				$update_y = get_post_meta($ID, $structure.'-'.$up.'-y', true);
				$update_level = get_post_meta($ID, $structure.'-'.$up.'-level', true);
				update_post_meta($ID, $structure.'-'.$i.'-x', $update_x);
				update_post_meta($ID, $structure.'-'.$i.'-y', $update_y);
				update_post_meta($ID, $structure.'-'.$i.'-level', $update_level);
			// Delete the highest one (since it's shifted down)
			} elseif ($i == $num) {
				delete_post_meta($ID, $structure.'-'.$i.'-x');
				delete_post_meta($ID, $structure.'-'.$i.'-y');
				delete_post_meta($ID, $structure.'-'.$i.'-level');
			}
		}

		// Reset level to 0
		update_post_meta($ID, $structure.'-'.$id.'-level', 0);
		
		// Update target population
		$target_current = get_post_meta($ID, 'target-pop', true);
		update_post_meta($ID, 'target-pop', $target_current + $level * $target_decrease);

		// Update population for residential types
		if ($structure = 'neighborhood') {
			$pop = get_post_meta($ID, 'population', true);
			update_post_meta($ID, 'population', $level * ($pop-20));
		}
	}


}

?>