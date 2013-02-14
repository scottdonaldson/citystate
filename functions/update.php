<?php
define('MAIN', dirname(__FILE__) . '/');

// Reset all users' turns to 10
$users = get_users();
foreach ($users as $user) {
	update_user_meta($user->ID, 'turns', 10);

	// Scouting territories
	if (get_user_meta($user->ID, 'scouting', true) == 'yes') {

		// Send the message for notification purposes
		$to = $user->ID;
		$subject = 'Scout Report';
		$region = get_user_meta($user->ID, 'scouting_region', true);
		$x = get_user_meta($user->ID, 'scouting_x', true);
		$y = get_user_meta($user->ID, 'scouting_y', true);

		include( MAIN . 'maps/'.$region.'.php');
		$resources = $map[$y][$x - 1][1];
		$count = count($resources);
		$list = array(); // create an empty array that will store the resources
		$i = 0;
		foreach ($resources as $resource => $value) {
			if ($i == $count - 1) {
				$joiner = '.';
			} elseif ($i == $count - 2 && $count != 2) {
				$joiner = ', and ';
			} elseif ($i == $count - 2 && $count == 2) {
				$joiner = ' and ';
			} else {
				$joiner = ', ';
			}
	    	if ($value < 4) {
	    		array_push($list, 'scarce amounts of <strong>'.$resource.'</strong>'.$joiner);
	    	} elseif ($value >= 4 && $value < 7) {
	    		array_push($list, 'medium amounts of <strong>'.$resource.'</strong>'.$joiner);
	    	} elseif ($value >= 7) {
	    		array_push($list, 'large amounts of <strong>'.$resource.'</strong>'.$joiner);
	    	}
	    	$i++;
	    }
	    $list = implode($list);

		$content = '<p>Scouts have completed their report of the territory as you requested. The territory&apos;s natural resources include '.$list.'</p>'.
			'<p><a href="'.home_url().'/'.$region.'?x='.$x.'&y='.$y.'">View highlighted territory on the map</a>.</p>';

		
		$ID = wp_insert_post(array(
			'post_type' => 'message',
			'post_title' => $subject,
			'post_content' => $content,
			'post_status' => 'publish'
			)
		);
		add_post_meta($ID, 'to', $to);
		add_post_meta($ID, 'from', 0);
		add_post_meta($ID, 'read', 'unread');

		// Add to user's list of scouted territories
		$scouted = get_user_meta($user->ID, 'scouted', true);
		update_user_meta($user->ID, 'scouted', $scouted + 1);

		update_user_meta($user->ID, 'scouted_'.$scouted.'-region', $region);
		update_user_meta($user->ID, 'scouted_'.$scouted.'-x', $x);
		update_user_meta($user->ID, 'scouted_'.$scouted.'-y', $y);
	}
	update_user_meta($user->ID, 'scouting', 'no');
}

// Update all cities
$city_query = new WP_query(array(
	'posts_per_page' => -1
	)
);
while ($city_query->have_posts()) : $city_query->the_post();

	// Get info
	$ID = get_the_ID();
	$user_ID = get_the_author_meta('ID');

	// Resets
	include ( MAIN . 'update/resets.php');

	// Resources
	include ( MAIN . 'resources.php');
	foreach ($resources as $key=>$resource) {
		if (get_post_meta($ID, $key, true) > 0) { 
			// see if there are structures mining for this particular resource
			if (get_post_meta($ID, $resource[1].'s', true) > 0) {
				$initial_val = get_post_meta($ID, $key, true);
				$funding = get_post_meta($ID, $resource[1].'-funding', true);
				$miners = get_post_meta($ID, $resource[1].'s', true);
				switch ($funding) {
					case 'bad':
						$result = $miners * floor(0.5 * $initial_val);
						break;
					case 'fair':
						$result = $miners * $initial_val;
						break;
					case 'good':
						$result = $miners * floor(1.5 * $initial_val);
						break;
					case 'excellent':
						$result = $miners * floor(2 * $initial_val);
						break;
				}
				update_post_meta($ID, $resource[0], get_post_meta($ID, $resource[0], true) + $result);
			}
		}
	}
	// Structures that cost resources
	$neighborhoods = get_post_meta($ID, 'neighborhoods', true);
	for ($i = 1; $i <= $neighborhoods; $i++) {
		// Only once- or twice-upgraded neighborhoods cost resources
		if (get_post_meta($ID, 'neighborhood-'.$i.'-level', true) == 1 ) {
			// Though we technically call it 'random', in actuality even-numbered takes food, odd- takes fish
			if ($i % 2 == 0) {
				update_post_meta($ID, 'food_stock', get_post_meta($ID, 'food_stock', true) - 1);
			} else {
				update_post_meta($ID, 'fish_stock', get_post_meta($ID, 'fish_stock', true) - 1);
			}
		// Twice-upgraded take one food AND one fish
		} elseif (get_post_meta($ID, 'neighborhood-'.$i.'-level', true) == 2 ) {
			update_post_meta($ID, 'food_stock', get_post_meta($ID, 'food_stock', true) - 1);
			update_post_meta($ID, 'fish_stock', get_post_meta($ID, 'fish_stock', true) - 1);
		}
	}
			
	// Get population, happiness, and target pop values
	$pop = get_post_meta($ID, 'population', true);
	$city_happy = get_post_meta($ID, 'happiness', true);
	$target = get_post_meta($ID, 'target-pop', true);
	
	// If target population is greater than population,
	// happiness helps it grow quicker
	if ($target >= $pop) {
		$newpop = $pop + ceil((0.00333 * $city_happy) * ($target - $pop));			
	// If target population is lower than population,
	// happiness slows down population loss
	} else {
		$newpop = $pop + floor((0.00333 * (100 - $city_happy)) * ($target - $pop));
	}
	update_post_meta($ID, 'population', $newpop);

	// Update target population based on each trade route in city
	if (get_post_meta($ID, 'traderoutes', true) > 0) {
		$trades = get_post_meta($ID, 'trade');
		foreach ($trades as $trade) {
			// Get some info about the trade partner
			$trade_pop = get_post_meta($trade, 'population', true);
			$trade_target = get_post_meta($trade, 'target-pop', true);
			$trade_happy = get_post_meta($trade, 'happiness', true);
			
			// Anticipate the partner's population change (NOT new pop)
			if ($trade_target >= $trade_pop) {
				$trade_change = ceil((0.00333 * $trade_happy) * ($trade_target - $trade_pop));
			} else {
				$trade_change = floor((0.00333 * (100 - $trade_happy)) * ($trade_target - $trade_pop));
			}
			$trade_change = floor(0.075 * $trade_change);

			update_post_meta($ID, 'target-pop', $target + $trade_change);

			// Update target pop again
			$target = get_post_meta($ID, 'target-pop', true);
		}
	}

	// Population milestones
	include ( MAIN . 'update/pop-milestones.php');
		
	// Taxes
	$cash = get_user_meta($user_ID, 'cash', true);
	$taxes = ceil(0.05*$pop);
	update_user_meta($user_ID, 'cash', $cash + $taxes);

	// Structure-related
	include ( MAIN . 'structures.php' );
	foreach ($structures as $structure=>$values) {
		include( MAIN .'structures/values.php');

		// Non-repeating structures
		if ($max == 1) {
			// Assign some variables.
			// $y tells us the y position. If the structure hasn't been built, it's 0.
			// $cost is originally the cost of construction. Multiplying it by 0.02,
			//     it will here be used as our base upkeep costs.
			// $funding is the funding for the structure. If it hasn't been set, it's
			//     equal to the previously defined $cost
			$y = get_post_meta($ID, $structure.'-y', true);
			$cost = 0.02*$cost;
			$funding = get_post_meta($ID, 'funding-'.$structure, true) > 0 ? get_post_meta($ID, 'funding-'.$structure, true) : $cost;

			// If the structure has been built, we subtract upkeep costs/funding
			if ($y != 0) {
				$cash = get_user_meta($user_ID, 'cash', true);
				update_user_meta($user_ID, 'cash', $cash - $funding);
			}

			// If the population of the city is already at or above the point 
			// where the structure is desired, certain things start to happen.
			if ($pop >= $desired) {
				$city_happy = get_post_meta($ID, 'happiness', true);

				// If the structure hasn't been built, we reduce happiness by 5%
				if ($y == 0) {				
					update_post_meta($ID, 'happiness', floor(0.95 * $city_happy));
				
				// If the structure has been built...
				} else {
					
					// For each 1000 people in the population greater than the 
					// desired population, base costs needed to keep the peace are 
					// increased by 10% (rounded to nearest 1)
					$need_funding = round($cost * (1 + 0.1 * (($pop - $desired) / 1000) ));

					// $diff is the percentage supplied vs. what is needed
					$diff = $funding / $need_funding;

					$city_happy = get_post_meta($ID, 'happiness', true);
					$city_culture = get_post_meta($ID, 'culture', true);
					$city_edu = get_post_meta($ID, 'education', true);

					// If the funding supplied for this structure is less than required,
					// people get unhappy, less cultural, and stupider.
					if ($diff < 1) {
						update_post_meta($ID, $structure.'-funding', 'bad');
						
						// To determine the reduction:
						// Take original city happy/culture/edu. 
						// Take away 1/2 of original structural increase.
						// Take weighted average of $diff and 1 (i.e. 70% -> 90%) percentage of that value.
						update_post_meta($ID, 'happiness', round($city_happy * (1 - 0.005*$happy) * (2 + $diff)/3), 3);
						update_post_meta($ID, 'culture', round($city_culture * (1 - 0.005*$culture) * (2 + $diff)/3), 3);
						update_post_meta($ID, 'education', round($city_edu * (1 - 0.005*$edu) * (2 + $diff)/3), 3);

					// Between 100% and 150%, funding is fair and no values change
					} elseif ($diff >= 1 && $diff < 1.5) {
						update_post_meta($ID, $structure.'-funding', 'fair');

					// Between 150% and 300%, funding is good and values change
					} elseif ($diff >= 1.5) {
						update_post_meta($ID, $structure.'-funding', 'good');

						// Increase is similar to building a structure but factoring in
						// our new friend $diff
						update_post_meta($ID, 'happiness', $city_happy + round(0.04 * $happy * ($diff - 1.5) * (1 - 0.01*$city_happy), 3));
						update_post_meta($ID, 'culture', $city_culture + round(0.04 * $culture * ($diff - 1.5) * (1 - 0.01*$city_culture), 3));
						update_post_meta($ID, 'education', $city_edu + round(0.04 * $edu * ($diff - 1.5) * (1 - 0.01*$city_edu), 3));
					} 
					// Above 500%, funding is EXCELLENT
					if ($diff >= 5) {
						update_post_meta($ID, $structure.'-funding', 'excellent');
					}
				
				} // end structure has been built
			
			} // end is current pop greater than desired pop

		// Repeating structures
		$multiples = array('park', 'farm', 'fishery', 'lumberyard', 'port');
		} elseif (in_array($structure, $multiples)) {
			
			// $count tells us how many have been built.
			// $cost is originally the cost of construction. Multiplying it by 0.02,
			//     it will here be used as our base upkeep costs.
			// Here, funding is for ALL the structures together (or, if it hasn't been set, it's the 
			//     base upkeep times the number of structures)
			$count = get_post_meta($ID, $structure.'s', true);
			$cost = 0.02 * $cost;
			$funding = get_post_meta($ID, 'funding-'.$structure, true) > 0 ? get_post_meta($ID, 'funding-'.$structure, true) : $cost * $count;

			// If the structure has been built, we subtract upkeep costs/funding
			if ($count > 0) {
				$cash = get_user_meta($user_ID, 'cash', true);
				update_user_meta($user_ID, 'cash', $cash - $funding);

				// If the population of the city is already at or above the point 
				// where the structure is desired, certain things start to happen.
				if ($pop >= $desired) {

					// For each 1000 people in the population greater than the 
					// desired population, base costs needed to keep the peace are 
					// increased by 10% (rounded to nearest 1)
					$need_funding = $count * round($cost * (1 + 0.1 * (($pop - $desired) / 1000) ));

					// $diff is the percentage supplied vs. what is needed
					$diff = $funding / $need_funding;

					$city_happy = get_post_meta($ID, 'happiness', true);
					$city_culture = get_post_meta($ID, 'culture', true);
					$city_edu = get_post_meta($ID, 'education', true);

					// If the funding supplied for this structure is less than required,
					// people get unhappy, less cultural, and stupider.
					if ($diff < 1) {
						update_post_meta($ID, $structure.'-funding', 'bad');
						
						// To determine the reduction:
						// Take original city happy/culture/edu. 
						// Take away 1/2 of original structural increase.
						// Take weighted average of $diff and 1 (i.e. 70% -> 90%) percentage of that value.
						update_post_meta($ID, 'happiness', round($city_happy * (1 - 0.005*$count*$happy) * (2 + $diff)/3), 3);
						update_post_meta($ID, 'culture', round($city_culture * (1 - 0.005*$count*$culture) * (2 + $diff)/3), 3);
						update_post_meta($ID, 'education', round($city_edu * (1 - 0.005*$count*$edu) * (2 + $diff)/3), 3);

					// Between 100% and 150%, funding is fair and no values change
					} elseif ($diff >= 1 && $diff < 1.5) {
						update_post_meta($ID, $structure.'-funding', 'fair');

					// Between 150% and 300%, funding is good and values change
					} elseif ($diff >= 1.5) {
						update_post_meta($ID, $structure.'-funding', 'good');

						// Increase is similar to building a structure but factoring in
						// our new friend $diff
						update_post_meta($ID, 'happiness', $city_happy + $count*round(0.04 * $happy * ($diff - 1.5) * (1 - 0.01*$city_happy), 3));
						update_post_meta($ID, 'culture', $city_culture + $count*round(0.04 * $culture * ($diff - 1.5) * (1 - 0.01*$city_culture), 3));
						update_post_meta($ID, 'education', $city_edu + $count*round(0.04 * $edu * ($diff - 1.5) * (1 - 0.01*$city_edu), 3));
					} 
					// Above 500%, funding is EXCELLENT
					if ($diff >= 5) {
						update_post_meta($ID, $structure.'-funding', 'excellent');
					}
				
				} // end is current pop greater than desired pop

			} // end is $count greater than 0

		} // End non-repeating or repeating structure

	} // end structure array foreach

endwhile;
wp_reset_postdata();
?>
