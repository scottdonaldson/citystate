<?php

// If it's the first time visiting their city
function check_for_welcome() {
	if (isset($_GET['visit']) && $_GET['visit'] == 'first') {
		$alert = '<h2>Welcome to your new city!</h2>';
	}
}

// Function to display the map
function show_city_map($ID) {
	foreach ( range(1,100) as $tile ) { 
		$x = fmod($tile, 10);
		$y = ceil($tile/10);

		// If at the beginning of a row, open the row
		if ($x == 1) { ?>
			<div class="row row-<?= $y; ?> clearfix">
		<?php 
		}

			// Show the tile
			show_city_tile($ID, $x, $y);

		// If at the end of a row, close the row
		if ($x == 0) { ?>
			</div><!-- .row -->
		<?php 
		}
	}
}

// Function for resetting the tile data
function reset_tile_data() {
	$resets = array(
		$tile_class, 
		$tile_data_structure, 
		$tile_data_cost, 
		$tile_data_upgrade,
		$tile_data_id
	);
	foreach ($resets as $reset) { $reset = ''; }
}

// Function for setting the tile data
function set_tile_data($structure, $ID, $x, $y) {
	// Non-repeating 
	if ($structure['max'] == 1) {
		if (meta($structure.'-x') == $x && meta($structure.'-y') == $y) {
			$tile_class = $structure.' structure no-build';
			$tile_data_structure = $structure;
			
			// If upgradeable
			if ($structure['upgrade'] && meta($structure.'-level') < $structure['upgrade']) {
				$tile_data_cost = $structure['cost'];
				$tile_data_upgrade = true;
				$tile_data_level = meta($structure.'-level');
			}
			break;
		}
	// Repeating
	} else {
		for ($i = 1; $i <= $total = meta($structure.'s'); $i++) {
			if (meta($structure.'-'.$i.'-x') == $x && meta($structure.'-'.$i.'-y') == $y) {
				$tile_class = $structure.' structure no-build';
				$tile_data_structure = $structure;
				$tile_data_id = $i;
				
				// If upgradeable
				if ($structure['upgrade'] && meta($structure.'-'.$i.'-level') < $structure['upgrade']) {
					$tile_data_cost = $structure['cost'];
					$tile_data_upgrade = true;
					$tile_data_level = meta($structure.'-'.$i.'-level');
				} 
				break;
			}
		}
	}	
}

// Function to display city tile (called 100 times in show_city_map())
function show_city_tile($ID, $x, $y) {

	// Is there a structure here?
	foreach($structures as $structure) {
		reset_tile_data();
		set_tile_data($structure, $ID, $x, $y);
	} ?>

		<div data-x="<?= $x; ?>" data-y="<?= $y; ?>" class="<?= $tile_class; ?>" data-structure="<?= $tile_data_structure; ?>" data-cost="<?= $tile_data_cost; ?>" data-upgrade="<?= $tile_data_upgrade; ?>" data-level="<?= $tile_data_level; ?>" data-id="<?= $i; ?>"></div>

	<?php
}

?>