<?php

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

// Function for setting the tile data
function get_tile_data($structure, $ID, $x, $y) {

	$tile_data = array();

	// Non-repeating 
	if ($structure['max'] == 1) {
		if (get_post_meta($ID, $structure['slug'].'-x', true) == $x && 
			get_post_meta($ID, $structure['slug'].'-y', true) == $y) {

			$tile_data['class'] = $structure['slug'].' structure no-build';
			$tile_data['structure'] = $structure['slug'];
			$tile_data['level'] = get_post_meta($ID, $structure['slug'].'-level', true);
			
			// If upgradeable
			if ($structure['upgrade'] && get_post_meta($ID, $structure['slug'].'-level', true) < $structure['upgrade']) {
				$tile_data['cost'] = $structure['cost'];
				$tile_data['upgrade'] = true;
			}
		}
	// Repeating
	} else {
		for ($i = 1; $i <= get_post_meta($ID, $structure['slug'].'-number', true); $i++) {

			if (get_post_meta($ID, $structure['slug'].'-'.$i.'-x', true) == $x && 
				get_post_meta($ID, $structure['slug'].'-'.$i.'-y', true) == $y) {

				$tile_data['class'] = $structure['slug'].' structure no-build';
				$tile_data['structure'] = $structure['slug'];
				$tile_data['id'] = $i;
				$tile_data['level'] = get_post_meta($ID, $structure['slug'].'-'.$i.'-level', true);
				
				// If upgradeable
				if ($structure['upgrade'] && get_post_meta($ID, $structure['slug'].'-'.$i.'-level', true) < $structure['upgrade']) {
					$tile_data['cost'] = $structure['cost'];
					$tile_data['upgrade'] = true;
				} 
			}
		}
	}
	return $tile_data;
}

// Function to display city tile (called 100 times in show_city_map())
function show_city_tile($ID, $x, $y) {

	// Is there a structure here?
	foreach (get_structures() as $structure) {
		$tile_data = get_tile_data($structure, $ID, $x, $y);
		if ($tile_data) { break; }
	} ?>

	<div data-x="<?= $x; ?>" data-y="<?= $y; ?>" class="tile <?= $tile_data['class']; ?>" data-structure="<?= $tile_data['structure']; ?>" data-cost="<?= $tile_data['cost']; ?>" data-upgrade="<?= $tile_data['upgrade']; ?>" data-level="<?= $tile_data['level']; ?>" data-id="<?= $tile_data['id']; ?>"></div>

	<?php
}

function show_city_neighbors($ID) {
	foreach (get_geo() as $cardinal) { 
		$terrain = get_post_meta($ID, 'map-'.$cardinal, true); ?>
		
		<div id="<?php echo $cardinal; ?>" class="terrain <?php echo $terrain; ?>"></div>
		<?php
	}
}

function show_build_form($ID) { 
	$pop = get_post_meta($ID, 'population', true);
	?>
	<form action="<?= get_permalink().'?structure=build'; ?>" method="POST">
		<?php 
		// Open the list
		echo '<ul>';
		foreach (get_structures() as $structure) { 

			$list_item = '<li id="'.$structure['slug'].'">'.ucwords($structure['name']).' ('.th($structure['cost']).')</li>';

			// Non-repeating structures
			if ($structure['max'] == 1) {	

				// Only show build option if structure is not yet built
				// and if has passed 1/2 of population at which it is desired
				if (!get_post_meta($ID, $structure['slug'].'-y', true) 
					&& $pop >= 0.5 * $structure['desired']) { 
					echo $list_item;
				}
				
			// Repeating structures
			} else { 
				
				// Only show if max is 0 (can build as many as desired)
				// or if the count is less than the maximum allowed
				// AND if has passed 1/2 of population at which it is desired
				if (($structure['max'] == 0 || get_post_meta($ID, $structure.'-number', true) < $structure['max']) && 
					$pop >= 0.5 * $desired) { 
					// Resource-related structures.
					if ($structure['resource'] != false) {
						// For resource structures, the resource must be present in the city
						// and the population greater than 1000
						if (get_post_meta($ID, $structure['resource'], true) > 0 && 
							$pop >= 1000) { 
							echo $list_item;
						}
					} else {
						echo $list_item;
					}
				}
			} // end repeating structures
		} // end foreach
		echo '</ul>';
		?>
		<input id="build-structure" name="build-structure" type="hidden" />
		<input id="build-x" name="build-x" type="hidden" />
		<input id="build-y" name="build-y" type="hidden" />	
		<p class="helper"></p>
		<input class="button" type="submit" value="build" name="update" />
	</form>
<?php
}

function show_extra_forms() { ?>
	<form class="upgrade" method="post" action="<?= get_permalink().'?structure=upgrade'; ?>">
		<input type="hidden" id="upgrade-structure" name="upgrade-structure" />
		<input id="upgrade-x" name="upgrade-x" type="hidden" />
		<input id="upgrade-y" name="upgrade-y" type="hidden" />
		<input id="upgrade-id" name="upgrade-id" type="hidden" />
		<input class="button" type="submit" value="Upgrade" name="update" />
	</form>
	<form method="post" action="<?= get_permalink().'?structure=demolish'; ?>">
		<input type="hidden" id="demolish-structure" name="demolish-structure" />
		<input id="demolish-x" name="demolish-x" type="hidden" />
		<input id="demolish-y" name="demolish-y" type="hidden" />
		<input id="demolish-id" name="demolish-id" type="hidden" />
		<p class="helper"></p>
		<input class="button" type="submit" value="Demolish (50)" name="update" />
	</form>
<?php
}

function show_city_admin($current_user, $ID, $resources) { 
	// .name-structure, .x, and .y are populated via JS
	$build_message = 'Build a structure at (<span class="x"></span>,&nbsp;<span class="y"></span>):';
	$extra_message = '<span class="name-structure"></span> at (<span class="x"></span>,&nbsp;<span class="y"></span>):';
	?>
	<!-- wrapping build and demolish/upgrade in one div -->
	<div>
		<div id="build" class="infobox">
			<p><?= $build_message; ?></p>
			<?php show_build_form($ID, $resources); ?>
		</div>

		<div id="extra" class="infobox">
			<p><?= $extra_message; ?></p>
			<?php show_extra_forms(); ?>
		</div>
	</div>

<?php
}

?>