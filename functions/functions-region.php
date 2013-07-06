<?php

// Get all the cities in the region
function get_region_cities($region) {
	$cities = array();
	$region_query = new WP_Query(array(
		'meta_key' => 'region',
		'meta_value' => $region,
		'posts_per_page' => -1
		)
	);
	while ($region_query->have_posts()) : $region_query->the_post();
		$ID = get_the_ID();
		// Set key of array to x,y
		$cities[get_post_meta($ID, 'location-x', true).','.get_post_meta($ID, 'location-y', true)] = array(
			'name' => get_the_title(),
			'ID' => $ID,
			'user' => get_the_author_id(),
			'link' => get_permalink(),
			'population' => get_post_meta($ID, 'population', true)
			);
	endwhile;
	wp_reset_postdata();

	return $cities;
}

// Function to display the map
function show_region_map($user, $ID) {

	$cities = get_region_cities($ID);

	foreach ( range(1, 100) as $tile ) { 
		$x = fmod($tile, 10) == 0 ? 10 : fmod($tile, 10);
		$y = ceil($tile/10);

		$city = $cities[$x.','.$y];

		// If at the beginning of a row, open the row
		if ($x == 1) { ?>
			<div class="row row-<?= $y; ?> clearfix">
		<?php 
		}

			// Show the tile, with extra stuff if it's on single-region.php
			// (meaning it's an admin editing the region)
			if (current_user_can('switch_themes') && $_GET['edit'] == 'true') {
				show_admin_region_tile($ID, $x, $y);
			} else {
				show_region_tile($user, $ID, $x, $y, $city);
			}

		// If at the end of a row, close the row
		if ($x == 10) { ?>
			</div><!-- .row -->
		<?php 
		}
	}
}

function show_admin_region_tile($ID, $x, $y) { 

	$key = $x.','.$y;

	$tile_terrain = get_post_meta($ID, $x.','.$y.'-terrain', true);
	$tile_resources = array();
	foreach (get_resources() as $resource => $values) { 
		$tile_resources[$resource] = 'data-'.$resource.'="'.get_post_meta($ID, $key.'-'.$resource, true).'" ';
	}
	?>
	<div data-x="<?= $x; ?>" data-y="<?= $y; ?>" class="tile" data-terrain="<?= $tile_terrain; ?>" <?php foreach ($tile_resources as $output) { echo $output; } ?>>
		<div class="fields">
			<div class="alignleft">
				<?php
				foreach (get_terrain() as $terrain) { 
					$checked = $tile_terrain == $terrain ? 'checked' : '';
					?>
					<label>
						<input type="radio" name="<?= $key; ?>-terrain" value="<?= $terrain; ?>" <?= $checked; ?>>
						<?= ucfirst($terrain); ?>
					</label><?php
				}
				?>
			</div>
			<div class="alignright">
				<?php
				foreach (get_resources() as $resource => $values) { 
					$has_resource = get_post_meta($ID, $key.'-'.$resource, true) ? true : false; ?>
					<div class="clearfix">
						<?php if ($has_resource) { ?>
							<input type="checkbox" id="<?= $key.'-'.$resource; ?>" checked><?= ucfirst($resource); ?>
							<input type="number" name="<?= $key.'-'.$resource; ?>" value="<?= get_post_meta($ID, $key.'-'.$resource, true); ?>">
						<?php } else { ?>
							<input type="checkbox" id="<?= $key.'-'.$resource; ?>"><?= ucfirst($resource); ?>
						<?php } ?>
					</div><?php
				}
				?>
			</div>
		</div>
		<input type="hidden" id="<?= $key.'-terrain'; ?>" name="<?= $key.'-terrain'; ?>" value="<?= $tile_terrain; ?>">
	</div>
<?php }

function show_region_overlays() { ?>
	<h2>Data overlays:</h2>
	<ul id="overlays">
		<?php
		foreach (get_resources() as $resource => $values) { ?>
			<li data-overlay="<?= $resource; ?>"><?= ucfirst($resource); ?></li>
		<?php } ?>
		<li data-overlay="remove">Remove overlay</li>
	</ul>
<?php }

function show_region_tile($user, $ID, $x, $y, $city) { 
	$terrain = get_post_meta($ID, $x.','.$y.'-terrain', true);
	?>
	<div data-x="<?= $x; ?>" data-y="<?= $y; ?>" class="tile" data-terrain="<?= $terrain; ?>">
		<?php if ($city) { 
			$city_pop_class = get_post_meta($city['ID'], 'population', true) <= 5000 ? floor(get_post_meta($city['ID'], 'population', true)/1000) : 5;
			$city_user_class = $user->ID == $city['user'] ? 'user-city' : 'not-user-city';
			?>
			<a href="<?= $city['link']; ?>" class="city <?= 'pop-0'.$city_pop_class.' '.$city_user_class; ?>"></a>
		<?php } ?>
	</div>
<?php }

function build_region($ID) {
	foreach ($_POST as $key => $value) {
		if ($value) {
			update_post_meta($ID, $key, $value);
		}
	}
}

// Query to get the region's neighbors
function get_region_neighbors($ID, $x, $y) {
	return new WP_Query(array(
		'post_type' => 'region',
		'meta_query' => array(
			array(
				'key' => 'POS-x',
				'value' => array($x - 1, $x, $x + 1),
				'compare' => 'IN'
			),
			array(
				'key' => 'POS-y',
				'value' => array($y - 1, $y, $y + 1),
				'compare' => 'IN'
			)
		),
		'posts_per_page' => -1,
		'post__not_in' => array($ID) // exclude the region we're looking at
		)
	);
}

function show_region_neighbors($user, $ID) {
	$x = get_post_meta($ID, 'POS-x', true);
	$y = get_post_meta($ID, 'POS-y', true);

	$neighbors = get_region_neighbors($ID, $x, $y);
	while ($neighbors->have_posts()) : $neighbors->the_post(); 
		$N_ID = get_the_ID();
		// WEST (and NW and SW)
		if (get_post_meta($N_ID, 'POS-x', true) == $x - 1) {
			if (get_post_meta($N_ID, 'POS-y', true) == $y - 1) {
				$cardinal = 'nw';
			} elseif (get_post_meta($N_ID, 'POS-y', true) == $y + 1) {
				$cardinal = 'sw';
			} else {
				$cardinal = 'w';
			}
		// NORTH OR SOUTH
		} elseif (get_post_meta($N_ID, 'POS-x', true) == $x) {
			if (get_post_meta($N_ID, 'POS-y', true) == $y - 1) {
				$cardinal = 'n';
			} else {
				$cardinal = 's';
			}
		// EAST (and NE and SE)
		} else {
			if (get_post_meta($N_ID, 'POS-y', true) == $y - 1) {
				$cardinal = 'ne';
			} elseif (get_post_meta($N_ID, 'POS-y', true) == $y + 1) {
				$cardinal = 'se';
			} else {
				$cardinal = 'e';
			}
		}
			?>
			<a id="<?= $cardinal; ?>" class="neighbor map" href="<?php the_permalink(); ?>">
				<?php show_region_map($user, $N_ID); ?>
			</a>
		<?php
		// }
	?>
	<?php
	endwhile;
	wp_reset_postdata();

	foreach (get_geo() as $cardinal) { 
		switch ($cardinal) {
			case 'nw': $x--; $y--; break;
			case 'n': $y--; 	   break;
			case 'ne': $x++; $y--; break;
			case 'w': $x--; 	   break;
			case 'e': $x++; 	   break;
			case 'sw': $x--; $y++; break;
			case 's': $y++; 	   break;
			case 'se': $x++; $y++; break;
		}	
	}	
}

// Function to calculate cost for user to build a new city
function new_city_cost($user) {
	$cities = count_user_posts($user->ID);
	return 1500 * $cities + 500;
}

// Show region admin function -- note that this is different from the "admin"
// above, as this is for any user who is logged in, to allow them to build
function show_region_admin($user, $ID) { ?>
	<div id="build" class="infobox">
		<p><span class="terrain"></span> at (<span class="x"></span>,&nbsp;<span class="y"></span>):</p>

		<form action="<?= the_permalink(); ?>" method="POST">
			<p>Build a city:</p>
			<input id="cityName" name="cityName" type="text" maxlength="25" />
			<input id="x" name="x" type="hidden" />
			<input id="y" name="y" type="hidden" />	
			<input id="region_id" name="region_id" value="<?= $ID; ?>" type="hidden" />	
			<input class="button" type="submit" id="buildCity" name="buildCity" value="Build City (<?= th(new_city_cost($user)); ?>)" />
		</form>
		<?php 
		// If scouts are already out in the territory, can't send more
		if (get_user_meta($current_user->ID, 'scouting', true) != 'yes') { ?>
		<small>or</small>
		<form action="<?= the_permalink(); ?>" method="POST">
			<input id="scout-x" name="scout-x" type="hidden">
			<input id="scout-y" name="scout-y" type="hidden">
			<input id="scout-region" name="scout-region" value="<?= $ID; ?>" type="hidden">
			<input class="button" type="submit" id="scout" name="scout" value="Scout Territory (350)">
		</form>
		<?php } ?>
	</div>
	<?php
}

// Checking if we're building a city
function check_for_build_city( $args ) {

	$user = $args['current_user'];
	$ID = $args['ID'];

	if (isset($_POST['buildCity'])) { 
	
		$cost = new_city_cost($user);

		// If we're good, proceed.
		if (no_bankrupt(get_user_meta($user->ID, 'cash', true), $cost)) {

			// First take the cash, then do stuff specific to each.
			update_user_meta($user->ID, 'cash', get_user_meta($user->ID, 'cash', true) - $cost);
			
			// Then build the city
			build_city($user, $ID, $_POST['x'], $_POST['y'], $resources, $geo);
		} else {
			return bankrupt_message();
		}
	} 
}

// building a new city
function build_city($user, $ID, $x, $y) {
	
	// Name of the city
	$title = $_POST['cityName'];

	// Insert new city
	$city_ID = wp_insert_post(array(
			'post_author' => $user->ID,
			'post_name' => create_slug($title),
			'post_title' => $title,
			'post_status' => 'publish'
		)	
	);

	// Get URL (will redirect to this later)
	$url = get_permalink($city_ID);

	// Set location of city based on what user
	// selected on main map, set pop. to 0
	add_post_meta($city_ID, 'region', $ID);
	add_post_meta($city_ID, 'location-x', $x);
	add_post_meta($city_ID, 'location-y', $y);
	add_post_meta($city_ID, 'target-pop', 1000);
	add_post_meta($city_ID, 'happiness', 50);

	// Set natural resources based on region info
	foreach (get_resources() as $resource => $values) {
		if (get_post_meta($ID, $x.','.$y.'-'.$resource, true)) {
			add_post_meta($city_ID, $resource, get_post_meta($ID, $x.','.$y.'-'.$resource, true));
		}
	}

	// Set geographic characteristics...
	build_city_neighbors($ID, $x, $y, $city_ID);

	// Update the activity log. 
	log_city($user, $city_ID, $ID);

	// Redirect
	header('Location: '.$url.'?visit=first');
}

// Function to get a region's specific neighbor in a certain (defined) direction
function get_region_neighbor_ID($ID, $x, $y) {
	$neighbor = new WP_Query(array(
		'post_type' => 'region',
		'meta_query' => array(
			array(
				'key' => 'POS-x',
				'value' => $x
			),
			array(
				'key' => 'POS-y',
				'value' => $y
			)
		),
		'posts_per_page' => 1
	));
	if ($neighbor->have_posts()) {
		while ($neighbor->have_posts()) : $neighbor->the_post();
			$neighbor_ID = get_the_ID();
		endwhile;
	} else {
		$neighbor_ID = false;
	}
	wp_reset_postdata();

	return $neighbor_ID;
}

// Function to set geographic characteristics of the city's neighbors
function build_city_neighbors($ID, $x, $y, $city_ID) {

	$reg_x = get_post_meta($ID, 'POS-x', true);
	$reg_y = get_post_meta($ID, 'POS-y', true);

	foreach (get_geo() as $cardinal) {

		switch ($cardinal) {
			case 'nw':
				// Are we in the upper-left corner?
				if ($x == 1 && $y == 1) {
					$neighbor_ID = get_region_neighbor_ID($ID, $reg_x - 1, $reg_y - 1);
					$val = $neighbor_ID ? get_post_meta($neighbor_ID, '10,10-terrain', true) : 'water';
				// In the leftmost column?
				} elseif ($x == 1) {
					$neighbor_ID = get_region_neighbor_ID($ID, $reg_x - 1, $reg_y);
					$val = $neighbor_ID ? get_post_meta($neighbor_ID, '10,'.($y - 1).'-terrain', true) : 'water';
				// In the top row?
				} elseif ($y == 1) {
					$neighbor_ID = get_region_neighbor_ID($ID, $reg_x, $reg_y - 1);
					$val = $neighbor_ID ? get_post_meta($neighbor_ID, ($x - 1).',10-terrain', true) : 'water';
				} else {
					$val = get_post_meta($ID, ($x - 1).','.($y - 1).'-terrain', true);
				}
				break;
			case 'n':
				// Are we in the top row?
				if ($y == 1) {
					$neighbor_ID = get_region_neighbor_ID($ID, $reg_x, $reg_y - 1);
					$val = $neighbor_ID ? get_post_meta($neighbor_ID, $x.',10-terrain', true) : 'water';
				} else {
					$val = get_post_meta($ID, $x.','.($y - 1).'-terrain', true);
				}
				break;
			case 'ne':
				// Are we in the upper-right corner?
				if ($x == 10 && $y == 1) {
					$neighbor_ID = get_region_neighbor_ID($ID, $reg_x + 1, $reg_y - 1);
					$val = $neighbor_ID ? get_post_meta($neighbor_ID, '1,10-terrain', true) : 'water';
				// In the rightmost column?	
				} elseif ($x == 10) {
					$neighbor_ID = get_region_neighbor_ID($ID, $reg_x + 1, $reg_y);
					$val = $neighbor_ID ? get_post_meta($neighbor_ID, '1,'.($y - 1).'-terrain', true) : 'water';
				// In the top row?	
				} elseif ($y == 1) {
					$neighbor_ID = get_region_neighbor_ID($ID, $reg_x, $reg_y - 1);
					$val = $neighbor_ID ? get_post_meta($neighbor_ID, ($x + 1).',10-terrain', true) : 'water';
				} else {
					$val = get_post_meta($ID, ($x + 1).','.($y - 1).'-terrain', true);
				}
				break;
			case 'w':
				// Are we in the leftmost column?
				if ($x == 1) {
					$neighbor_ID = get_region_neighbor_ID($ID, $reg_x - 1, $reg_y);
					$val = $neighbor_ID ? get_post_meta($neighbor_ID, '10,'.$y.'-terrain', true) : 'water';
				} else {
					$val = get_post_meta($ID, ($x - 1).','.$y.'-terrain', true);
				}
				break;
			case 'e':
				// Are we in the rightmost column?
				if ($x == 10) {
					$neighbor_ID = get_region_neighbor_ID($ID, $reg_x + 1, $reg_y);
					$val = $neighbor_ID ? get_post_meta($neighbor_ID, '1,'.$y.'-terrain', true) : 'water';
				} else {
					$val = get_post_meta($ID, ($x + 1).','.$y.'-terrain', true);
				}
				break;
			case 'sw':
				// Are we in the bottom left corner?
				if ($x == 1 && $y == 10) {
					$neighbor_ID = get_region_neighbor_ID($ID, $reg_x - 1, $reg_y + 1);
					$val = $neighbor_ID ? get_post_meta($neighbor_ID, '10,1-terrain', true) : 'water';
				// Leftmost column?
				} elseif ($x == 1) {
					$neighbor_ID = get_region_neighbor_ID($ID, $reg_x - 1, $reg_y);
					$val = $neighbor_ID ? get_post_meta($neighbor_ID, '10,'.($y + 1).'-terrain', true) : 'water';
				// Bottom row?
				} elseif ($y == 10) {
					$neighbor_ID = get_region_neighbor_ID($ID, $reg_x, $reg_y + 1);
					$val = $neighbor_ID ? get_post_meta($neighbor_ID, ($x - 1).',1-terrain', true) : 'water';
				} else {
					$val = get_post_meta($ID, ($x - 1).','.($y + 1).'-terrain', true);
				}
				break;
			case 's':
				// Are we in the bottom row?
				if ($y == 10) {
					$neighbor_ID = get_region_neighbor_ID($ID, $reg_x, $reg_y + 1);
					$val = $neighbor_ID ? get_post_meta($neighbor_ID, $x.',1-terrain', true) : 'water';
				} else {
					$val = get_post_meta($ID, $x.','.($y + 1).'-terrain', true);
				}
				break;
			case 'se':
				// Are we in the bottom right corner?
				if ($x == 10 && $y == 10) {
					$neighbor_ID = get_region_neighbor_ID($ID, $reg_x + 1, $reg_y + 1);
					$val = $neighbor_ID ? get_post_meta($neighbor_ID, '1,1-terrain', true) : 'water';
				// In the right most column?
				} elseif ($x == 10) {
					$neighbor_ID = get_region_neighbor_ID($ID, $reg_x, $reg_y + 1);
					$val = $neighbor_ID ? get_post_meta($neighbor_ID, '1,'.($y + 1).'-terrain', true) : 'water';
				// In the bottom row?
				} elseif ($y == 10) {
					$neighbor_ID = get_region_neighbor_ID($ID, $reg_x + 1, $reg_y);
					$val = $neighbor_ID ? get_post_meta($neighbor_ID, ($x + 1).',1-terrain', true) : 'water';
				} else {
					$val = get_post_meta($ID, ($x + 1).','.($y + 1).'-terrain', true);
				}
				break;
		}	

		$val = $val == 'water' ? 'water' : 'land';

		update_post_meta($city_ID, 'map-'.$cardinal, $val);
	}
}

// Update the activity log
function log_city($user, $city, $region) {
	// Update the activity log. The output:
	$site_url = home_url();

	$city = get_post($city);
	$region = get_post($region);

	$output = '<strong>The city of <a href="'.$city->guid.'">'.$city->post_title.'</a> was built in '.$region->guid.' by <a href="'.$site_url.'/user/'.$user->user_login.'">'.$user->display_name.'</a>.</strong>';

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
}


?>