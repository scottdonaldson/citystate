<?php

// Check for a structure update.
function check_for_structure( $args ) {

	$user = $args['current_user'];
	$ID = $args['ID'];

	if (isset($_POST['update']) && is_singular('post')) { 
		$type = $_GET['structure'];

		// If building a 2x2 structure
		if (isset($_POST['build-x-3']) && isset($_POST['build-y-3'])) {
			return do_structure($user, $type, $_POST['build-structure'], $ID, min($_POST['build-x'], $_POST['build-x-1'], $_POST['build-x-2'], $_POST['build-x-3']), min($_POST['build-y'], $_POST['build-y-1'], $_POST['build-y-2'], $_POST['build-y-3']));
		} else {
			return do_structure($user, $type, $_POST[$type.'-structure'], $ID, $_POST[$type.'-x'], $_POST[$type.'-y'], get_post_meta($ID, $structure.'-level', true));
		}
	} 
}

/* Master function for doing something with structures.

   Requires as parameters:
   - The $user object
   - What we're doing (building, demolishing, upgrading)
   - the $structure (slug)
   - The $ID of the post
   - The $x location
   - The $y location
   Optional:
   - The current $level of the structure (upgrading only) */

function do_structure($user, $what, $structure, $ID, $x, $y, $level) {

	$structure = get_structures()[$structure];
	// Make sure that user isn't going bankrupt.
	// $cost is defined in $structures array if building or upgrading,
	// is always 50 if demolishing.
	$cost = $what == 'demolish' ? 50 : $structure['cost'];

	// If we're good, proceed.
	if (no_bankrupt(get_user_meta($user->ID, 'cash', true), $cost)) {

		// First take the cash, then do stuff specific to each.
		update_user_meta($user->ID, 'cash', get_user_meta($user->ID, 'cash', true) - $cost);
		switch ($what) {
			case 'build':
				return build($user, $structure, $ID, $x, $y);
			case 'demolish':
				return demolish($user, $structure, $ID, $x, $y);
			case 'upgrade':
				return upgrade($user, $structure, $ID, $x, $y, $level);
		}
	} else {
		return bankrupt_message();
	}
}

// Function for updating the activity log depending on what we're doing.
function log_structure($user, $what, $structure) {
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
	$output = 'A '.$structure['name'].' was '.$verb.' in <a class="snapshot" href="'.$link.'">'.$city.'</a> by <a href="'.home_url().'/user/'.$user->user_login.'">'.$user->display_name.'</a>.';

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
		$already = get_post_meta(get_the_ID(), $user->user_login.'-'.$city.'-'.$what.'-'.$structure['slug'], true);
		if ($already > 0) {
			$new = $already + 1;
			$output = $new.' '.$structure['plural'].' were '.$verb.' in <a href="'.$link.'">'.$city.'</a> by <a href="'.home_url().'/user/'.$user->user_login.'">'.$user->display_name.'</a>.';
			delete_post_meta(get_the_ID(), 'activity', 'A '.$structure['name'].' was '.$verb.' in <a href="'.$link.'">'.$city.'</a> by <a href="'.home_url().'/user/'.$user->user_login.'">'.$user->display_name.'</a>.');
			delete_post_meta(get_the_ID(), 'activity', $already.' '.$structure['plural'].' were '.$verb.' in <a href="'.$link.'">'.$city.'</a> by <a href="'.home_url().'/user/'.$user->user_login.'">'.$user->display_name.'</a>.');
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

	// Get city population
	$pop = get_post_meta($ID, 'population', true);
		
	// Set location for non-repeating
	if ($structure['max'] == 1) {
		update_post_meta($ID, $structure['slug'].'-x', $x);
		update_post_meta($ID, $structure['slug'].'-y', $y);

	// Set location for repeating
	} else {

		$num = get_post_meta($ID, $structure['slug'].'-number', true);
		$num++;
		
		// Add location of new structure
		add_post_meta($ID, $structure['slug'].'-'.$num.'-x', $x);
		add_post_meta($ID, $structure['slug'].'-'.$num.'-y', $y);

		// Update total number
		update_post_meta($ID, $structure['slug'].'-number', $num);

		// Update population for residential types
		if ($structure['slug'] == 'neighborhood') {
			update_post_meta($ID, 'population', $pop + 20);
		}

	}

	// Update target population
	$target_current = get_post_meta($ID, 'target-pop', true);
	update_post_meta($ID, 'target-pop', $target_current + $structure['target']);

	// Update happiness, culture, education
	$happy = get_post_meta($ID, 'happiness', true);
	update_post_meta($ID, 'happiness', $happy + round($happy_increase - $structure['happy'] * $happy/100, 3));
	
	$culture = get_post_meta($ID, 'culture', true);
	update_post_meta($ID, 'culture', $culture + round($cult_increase - $structure['culture'] * $culture/100, 3));

	$edu = get_post_meta($ID, 'education', true);
	update_post_meta($ID, 'education', $edu + round($edu_increase - $structure['edu'] * $edu/100, 3));	

	// Update funding
	update_post_meta($ID, $structure['slug'].'-funding', get_funding($ID, $structure));

	log_structure($user, 'build', $structure);

}

function demolish($user, $structure, $ID, $x, $y) {

	if ($x == 10) { $x = 0; }
	
	// For non-repeating structures, just remove (set location back to (0,0))
	if ($structure['max'] == 1) {
		delete_post_meta($ID, $structure['slug'].'-x');
		delete_post_meta($ID, $structure['slug'].'-y');

		// Reset funding to 0
		update_post_meta($ID, 'funding-'.$structure['slug'], 0);
		update_post_meta($ID, $structure['slug'].'-funding', 0);

	// For repeating structures...
	} else {
		$num = get_post_meta($ID, $structure['slug'].'-number', true);
		$new = $num - 1;
		$id = $_POST['demolish-id'];

		$level = get_post_meta($ID, $structure['slug'].'-'.$id.'-level', true) + 1; 
			// Plus one so that meta level of 0 becomes 1, 1 becomes 2, etc. (for target pop multipliers)

		// Update total number
		update_post_meta($ID, $structure['slug'].'-number', $num);

		// Remove from meta and reset level to 0 (may change based on shifting in next step)
		update_post_meta($ID, $structure['slug'].'-'.$id.'-x', 0);
		update_post_meta($ID, $structure['slug'].'-'.$id.'-y', 0);
		update_post_meta($ID, $structure['slug'].'-'.$id.'-level', 0);

		// Shift all greater than $id down by 1
		for ($i = $id; $i <= $num; $i++) {
			if ($i < $num) {
				$up = $i + 1;
				// x, y, and level values all inherit those of the structure with an id of one greater
				update_post_meta($ID, $structure['slug'].'-'.$i.'-x', get_post_meta($ID, $structure['slug'].'-'.$up.'-x', true));
				update_post_meta($ID, $structure['slug'].'-'.$i.'-y', get_post_meta($ID, $structure['slug'].'-'.$up.'-y', true));
				update_post_meta($ID, $structure['slug'].'-'.$i.'-level', get_post_meta($ID, $structure['slug'].'-'.$up.'-level', true));
			// Delete the highest one (since it's shifted down)
			} else {
				delete_post_meta($ID, $structure['slug'].'-'.$i.'-x');
				delete_post_meta($ID, $structure['slug'].'-'.$i.'-y');
				delete_post_meta($ID, $structure['slug'].'-'.$i.'-level');
			}
		}

		// Update population for residential types
		if ($structure == 'neighborhood') {
			$pop = get_post_meta($ID, 'population', true);
			update_post_meta($ID, 'population', $pop - ($level * 20));
		}
	}

	// Update target population
	$target_current = get_post_meta($ID, 'target-pop', true);
	update_post_meta($ID, 'target-pop', $target_current - $level * $structure['target']);

	// Update happiness, culture, education
	$happy = get_post_meta($ID, 'happiness', true);
	update_post_meta($ID, 'happiness', round(100 * ( ($happy - $structure['happy']) / (100 - $structure['happy']) ), 3));
	
	$culture = get_post_meta($ID, 'culture', true);
	update_post_meta($ID, 'culture', round(100 * ( ($culture - $structure['culture']) / (100 - $structure['culture']) ), 3));

	$edu = get_post_meta($ID, 'education', true);
	update_post_meta($ID, 'education', round(100 * ( ($edu - $structure['edu']) / (100 - $structure['edu']) ), 3));

	log_structure($user, 'demolish', $structure);
}

function upgrade($user, $structure, $ID, $x, $y, $level) {

	$id = $_POST['upgrade-id'];
		
	if ($structures['max'] == 1) {
		
		// Get current level
		$level = get_post_meta($ID, $structure['slug'].'-level', true);

		// Increase level by 1
		update_post_meta($ID, $structure['slug'].'-level', $level + 1);

	// For repeating structures...
	} else {
		$num = get_post_meta($ID, $structure['slug'].'-number', true);

		// Get current level
		$level = get_post_meta($ID, $structure['slug'].'-'.$id.'-level', true);
		
		// Upgrading neighborhoods requires food and/or fish
		/*
		TODO: neighborhood resource shit

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
		*/

		// Increase level by 1
		update_post_meta($ID, $structure['slug'].'-'.$id.'-level', $level + 1);

		// Update population for residential types
		if ($structure['slug'] = 'neighborhood') {
			update_post_meta($ID, 'population', get_post_meta($ID, 'population', true) + 20);
		}
	}

	// Update target population
	$target_current = get_post_meta($ID, 'target-pop', true);
	update_post_meta($ID, 'target-pop', $target_current + $structure['target']);

	// Update happiness, culture, education
	$happy = get_post_meta($ID, 'happiness', true);
	update_post_meta($ID, 'happiness', $happy + round($happy_increase - $happy_increase * $happy/100, 3));
	
	$culture = get_post_meta($ID, 'culture', true);
	update_post_meta($ID, 'culture', $culture + round($cult_increase - $cult_increase * $culture/100, 3));

	$edu = get_post_meta($ID, 'education', true);
	update_post_meta($ID, 'education', $edu + round($edu_increase - $edu_increase * $edu/100, 3));	

	log_structure($user, 'upgrade', $structure);
}

?>