<?php

// Check for a structure update.
function check_for_structure($current_user) {
	if (isset($_POST['update']) && is_single()) { 
		$type = $_GET['structure'];

		// If building a 2x2 structure
		if (isset($_POST['build-x-3']) && isset($_POST['build-y-3'])) {
			do_structure($current_user, $type, $_POST['build-structure'], $post->ID, min($_POST['build-x'], $_POST['build-x-1'], $_POST['build-x-2'], $_POST['build-x-3']), min($_POST['build-y'], $_POST['build-y-1'], $_POST['build-y-2'], $_POST['build-y-3']));
		} else {
			do_structure($current_user, $type, $_POST[$type.'-structure'], $post->ID, $_POST[$type.'-x'], $_POST[$type.'-y'], get_post_meta($ID, $structure.'-level', true));
		}
	} 
}

/* Master function for doing something with structures.

   Requires as parameters:
   - What we're doing (building, demolishing, upgrading)
   - The $user object
   - The $ID of the post
   - The $x location
   - The $y location
   Optional:
   - The current $level of the structure (upgrading only) */

function do_structure($user, $what, $structure, $ID, $x, $y, $level) {

	// Make sure that user isn't going bankrupt.
	// $cost is defined in $structures array if building or upgrading,
	// is always 50 if demolishing.
	$cost = $what == 'demolish' ? $structures[$structure][3] : 50;
	no_bankrupt(get_user_meta($user->ID, 'cash', true) - $cost);

	// If we're good, proceed. First take the cash, then do stuff specific to each.
	update_user_meta($user->ID, 'cash', get_user_meta($user->ID, 'cash', true) - $cost);
	switch ($what) {
		case 'build':
			build($user, $structure, $ID, $x, $y);
			break;
		case 'demolish':
			demolish($user, $structure, $ID, $x, $y);
			break;
		case 'upgrade':
			upgrade($user, $structure, $ID, $x, $y, $level);
			break;
	}
}

// Function for updating the activity log depending on what we're doing.
function update_activity_log($user, $what, $structure) {
	switch ($what) {
		case 'build':
			$verb = 'built';
			break;
		case 'demolish':
			$verb = 'demolished';
			break;
		case 'upgrade':
			$verb = 'upgraded';
			break;
	}
	// Update the activity log. The output:
	$link = get_permalink();
	$city = get_the_title();
	$output = 'A '.$structures[$structure][0].' was '.$verb.' in <a href="'.$link.'">'.$city.'</a> by <a href="'.home_url().'/user/'.$user->user_login.'">'.$user->display_name.'</a>.';

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
		$already = get_post_meta(get_the_ID(), $user->user_login.'-'.$city.'-'.$what.'-'.$structure, true);
		if ($already > 0) {
			$new = $already + 1;
			$output = $new.' '.$structures[$structure][1].' were '.$verb.' in <a href="'.$link.'">'.$city.'</a> by <a href="'.home_url().'/user/'.$user->user_login.'">'.$user->display_name.'</a>.';
			delete_post_meta(get_the_ID(), 'activity', 'A '.$structures[$structure][0].' was '.$verb.' in <a href="'.$link.'">'.$city.'</a> by <a href="'.home_url().'/user/'.$user->user_login.'">'.$user->display_name.'</a>.');
			delete_post_meta(get_the_ID(), 'activity', $already.' '.$structures[$structure][1].' were '.$verb.' in <a href="'.$link.'">'.$city.'</a> by <a href="'.home_url().'/user/'.$user->user_login.'">'.$user->display_name.'</a>.');
			update_post_meta(get_the_ID(), $user->user_login.'-'.$city.'-'.$what.'-'.$structure, $new);
		} else {
			add_post_meta(get_the_ID(), $user->user_login.'-'.$city.'-'.$what.'-'.$structure, 1);
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
		add_post_meta(get_the_ID(), $user->user_login.'-'.$city.'-'.$what.'-'.$structure, 1);
	}
	endwhile;
	wp_reset_postdata();
}

// Function for building a new structure
function build($user, $structure, $ID, $x, $y) {

	// Helper variables
	$cost = $structures[$structure][3];
	$target_increase = $structures[$structure][4];
	$happy_increase = $structures[$structure][7];
	$cult_increase = $structures[$structure][8];
	$edu_increase = $structures[$structure][9];

	// Get city population
	$pop = get_post_meta($ID, 'population', true);
		
	// Set location for non-repeating
	if ($structures[$structure][2] == 1) {
		update_post_meta($ID, $structure.'-x', $x);
		update_post_meta($ID, $structure.'-y', $y);

		// Update target population
		$target_current = get_post_meta($ID, 'target-pop', true);
		update_post_meta($ID, 'target-pop', $target_current + $target_increase);

		// Update happiness, culture, education
		$happy = get_post_meta($ID, 'happiness', true);
		update_post_meta($ID, 'happiness', $happy + round($happy_increase - $happy_increase * $happy/100, 3));
		
		$culture = get_post_meta($ID, 'culture', true);
		update_post_meta($ID, 'culture', $culture + round($cult_increase - $cult_increase * $culture/100, 3));

		$edu = get_post_meta($ID, 'education', true);
		update_post_meta($ID, 'education', $edu + round($edu_increase - $edu_increase * $edu/100, 3));

		// Update funding
		$funding = round(0.02 * $cost * (1 + 0.1 * (($pop - $structures[$structure][6]) / 1000) ));
		if ($funding < 1) {
			$funding = 'bad';
		} elseif ($funding < 1.5) {
			$funding = 'fair';
		} elseif ($funding < 5) {
			$funding = 'good';
		} elseif ($funding >= 5) {
			$funding = 'excellent';
		}
		update_post_meta($ID, $structure.'-funding', $funding);

	// Set location for repeating
	} else {
		$num = get_post_meta($ID, $structure.'s', true);
		$new = $num + 1;
		
		// Add location of new structure
		add_post_meta($ID, $structure.'-'.$new.'-x', $x);
		add_post_meta($ID, $structure.'-'.$new.'-y', $y);

		// Update total number
		update_post_meta($ID, $structure.'s', $new);

		// Update target population
		$target_current = get_post_meta($ID, 'target-pop', true);
		update_post_meta($ID, 'target-pop', $target_current + $target_increase);

		// Update happiness, culture, education
		$happy = get_post_meta($ID, 'happiness', true);
		update_post_meta($ID, 'happiness', $happy + round($happy_increase - $happy_increase * $happy/100, 3));
		
		$culture = get_post_meta($ID, 'culture', true);
		update_post_meta($ID, 'culture', $culture + round($cult_increase - $cult_increase * $culture/100, 3));

		$edu = get_post_meta($ID, 'education', true);
		update_post_meta($ID, 'education', $edu + round($edu_increase - $edu_increase * $edu/100, 3));

		// Update population for residential types
		if ($structure == 'neighborhood') {
			update_post_meta($ID, 'population', $pop + 20);
		}

		// Update funding
		$funding = round(0.02 * $new * $cost * (1 + 0.1 * (($pop - $structures[$structure][6]) / 1000) ));
		if ($funding < 1) {
			$funding = 'bad';
		} elseif ($funding < 1.5) {
			$funding = 'fair';
		} elseif ($funding < 5) {
			$funding = 'good';
		} elseif ($funding >= 5) {
			$funding = 'excellent';
		}
		update_post_meta($ID, $structure.'-funding', $funding);
	}

	update_activity_log($user, 'build', $structure);

}

function demolish($user, $structure, $ID, $x, $y) {

	// Helper variables
	$cost = 50; // Flat cost of 50
	$target_decrease = -$structures[$structure][4]; // Note that it's negative! The opposite of the increase.
	$happy_decrease = -$structures[$structure][7];  // Same
	$cult_decrease = -$structures[$structure][8];	// as
	$edu_decrease = -$structures[$structure][9];	// above.

	if ($x == 10) { $x = 0; }

	// Take cash from user
	update_user_meta($user->ID, 'cash', $cash_current - $cost);
	
	// For non-repeating structures, just remove (set location back to (0,0))
	if ($structures[$structure][2] == 1) {
		update_post_meta($ID, $structure.'-x', 0);
		update_post_meta($ID, $structure.'-y', 0);

		$level = get_post_meta($ID, $structure.'-level', true) + 1;

		// Reset level and funding to 0
		update_post_meta($ID, $structure.'-level', 0);
		update_post_meta($ID, 'funding-'.$structure, 0);
		update_post_meta($ID, $structure.'-funding', 0);

		// Update target population
		$target_current = get_post_meta($ID, 'target-pop', true);
		update_post_meta($ID, 'target-pop', $target_current + $level * $target_decrease);

		// Update happiness, culture, education
		$happy = get_post_meta($ID, 'happiness', true);
		update_post_meta($ID, 'happiness', round(100 * ( ($happy + $happy_decrease) / (100 + $happy_decrease) ), 3));
		
		$culture = get_post_meta($ID, 'culture', true);
		update_post_meta($ID, 'culture', round(100 * ( ($culture + $cult_decrease) / (100 + $cult_decrease) ), 3));

		$edu = get_post_meta($ID, 'education', true);
		update_post_meta($ID, 'education', round(100 * ( ($edu + $edu_decrease) / (100 + $edu_decrease) ), 3));

	// For repeating structures...
	} else {
		$num = get_post_meta($ID, $structure.'s', true);
		$new = $num - 1;
		$level = get_post_meta($ID, $structure.'-'.$id.'-level', true) + 1; 
			// Plus one so that meta level of 0 becomes 1, 1 becomes 2, etc. (for target pop multipliers)

		// Update total number
		update_post_meta($ID, $structure.'s', $new);

		// Remove from meta and reset level to 0 (may change based on shifting in next step)
		delete_post_meta($ID, $structure.'-'.$id.'-x');
		delete_post_meta($ID, $structure.'-'.$id.'-y');
		update_post_meta($ID, $structure.'-'.$id.'-level', 0);

		// Shift all greater than $id down by 1
		for ($i = $id; $i <= $num; $i++) {
			if ($i < $num) {
				$up = $i + 1;
				$update_x = get_post_meta($ID, $structure.'-'.$up.'-x', true);
				$update_y = get_post_meta($ID, $structure.'-'.$up.'-y', true);
				$update_level = get_post_meta($ID, $structure.'-'.$up.'-level', true);
				update_post_meta($ID, $structure.'-'.$i.'-x', $update_x);
				update_post_meta($ID, $structure.'-'.$i.'-y', $update_y);
				update_post_meta($ID, $structure.'-'.$i.'-level', $update_level);
			// Delete the highest one (since it's shifted down)
			} else {
				delete_post_meta($ID, $structure.'-'.$i.'-x');
				delete_post_meta($ID, $structure.'-'.$i.'-y');
				delete_post_meta($ID, $structure.'-'.$i.'-level');
			}
		}
		
		// Update target population
		$target_current = get_post_meta($ID, 'target-pop', true);
		update_post_meta($ID, 'target-pop', $target_current + ($level * $target_decrease));

		// Update happiness, culture, education
		$happy = get_post_meta($ID, 'happiness', true);
		update_post_meta($ID, 'happiness', round(100 * ( ($happy + $happy_decrease) / (100 + $happy_decrease) ), 3));
		
		$culture = get_post_meta($ID, 'culture', true);
		update_post_meta($ID, 'culture', round(100 * ( ($culture + $cult_decrease) / (100 + $cult_decrease) ), 3));

		$edu = get_post_meta($ID, 'education', true);
		update_post_meta($ID, 'education', round(100 * ( ($edu + $edu_decrease) / (100 + $edu_decrease) ), 3));

		// Update population for residential types
		if ($structure == 'neighborhood') {
			$pop = get_post_meta($ID, 'population', true);
			update_post_meta($ID, 'population', $pop - ($level * 20));
		}
	}

	update_activity_log($user, 'demolish', $structure);
}

function upgrade($user, $structure, $ID, $x, $y, $level) {

	// Helper variables
	$cost = $structures[$structure][3];
	$target_increase = $structures[$structure][4];
	$happy_increase = $structures[$structure][7];
	$cult_increase = $structures[$structure][8];
	$edu_increase = $structures[$structure][9];
		
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
		update_post_meta($ID, 'happiness', $happy + round($happy_increase - $happy_increase * $happy/100, 3));
		
		$culture = get_post_meta($ID, 'culture', true);
		update_post_meta($ID, 'culture', $culture + round($cult_increase - $cult_increase * $culture/100, 3));

		$edu = get_post_meta($ID, 'education', true);
		update_post_meta($ID, 'education', $edu + round($edu_increase - $edu_increase * $edu/100, 3));

	// For repeating structures...
	} else {
		$num = get_post_meta($ID, $structure.'s', true);

		// Get current level
		$level = get_post_meta($ID, $structure.'-'.$id.'-level', true);
		
		// Upgrading neighborhoods requires food and/or fish
		if ($structure == 'neighborhood') {
			switch($level) {
				// If we're upgrading a fresh neighborhood, randomly pick food or fish and take 1
				case 0:
					$takes = array('food', 'fish');
					foreach ($takes as $take) {
						// If we're going bankrupt, stop and alert
						if (get_post_meta($ID, $take.'_stock', true) == 0) {
							// If we've reached fish, that means there was enough food but not fish,
							// so we return that food
							if ($take == 'fish') { 
								update_post_meta($ID, 'food_stock', get_post_meta($ID, 'food_stock', true) + 1); 
							}
							// Alert
							$alert = '<p>You need at least 1 food and 1 fish to upgrade that neighborhood.</p>';
							return false;
						} else {
							update_post_meta($ID, $take.'_stock', get_post_meta($ID, $take.'_stock', true) - 1);
						}
					}
					break;
				// Once-upgraded neighborhoods take 1 food, 1 fish, and 1 wool
				case 1:
					$takes = array('food', 'fish', 'wool');
					foreach ($takes as $take) {
						// If we're going bankrupt, stop and alert
						if (get_post_meta($ID, $take.'_stock', true) == 0) {
							// If we've reached fish, that means there was enough food but not fish,
							// so we return that food
							if ($take == 'fish') { 
								update_post_meta($ID, 'food_stock', get_post_meta($ID, 'food_stock', true) + 1); 
							}
							// Same as above for wool
							if ($take == 'wool') {
								update_post_meta($ID, 'food_stock', get_post_meta($ID, 'food_stock', true) + 1); 
								update_post_meta($ID, 'fish_stock', get_post_meta($ID, 'fish_stock', true) + 1);
							}
							// Alert
							$alert = '<p>You need at least 1 food, 1 fish, and 1 wool to upgrade that neighborhood.</p>';
							return false; 
						} else {
							update_post_meta($ID, $take.'_stock', get_post_meta($ID, $take.'_stock', true) - 1);
						}
					}
					break;
			}
		}

		// Increase level by 1
		update_post_meta($ID, $structure.'-'.$id.'-level', $level + 1);
		
		// Update target population
		$target_current = get_post_meta($ID, 'target-pop', true);
		update_post_meta($ID, 'target-pop', $target_current + $target_increase);

		// Update happiness, culture, education
		$happy = get_post_meta($ID, 'happiness', true);
		update_post_meta($ID, 'happiness', $happy + round($happy_increase - $happy_increase * $happy/100, 3));
		
		$culture = get_post_meta($ID, 'culture', true);
		update_post_meta($ID, 'culture', $culture + round($cult_increase - $cult_increase * $culture/100, 3));

		$edu = get_post_meta($ID, 'education', true);
		update_post_meta($ID, 'education', $edu + round($edu_increase - $edu_increase * $edu/100, 3));

		// Update population for residential types
		if ($structure = 'neighborhood') {
			$pop = get_post_meta($ID, 'population', true);
			update_post_meta($ID, 'population', $pop + 20);
		}
	}

	update_activity_log($user, 'upgrade', $structure);
}

?>