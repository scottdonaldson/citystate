<?php

// Get all the cities in the region
function get_region_cities($region) {

	return new WP_Query(array(
		'category_name' => $region,
		'posts_per_page' => -1,
		'order' => 'ASC'
		)
	);
}

// Function to display the map
function show_region_map($ID, $resources, $terrain) {

	foreach ( range(1,100) as $tile ) { 
		$x = fmod($tile, 10);
		$y = ceil($tile/10);

		// If at the beginning of a row, open the row
		if ($x == 1) { ?>
			<div class="row row-<?= $y; ?> clearfix">
		<?php 
		}

			// Show the tile, with extra stuff if it's on single-region.php
			// (meaning it's an admin editing the region)
			if (is_single()) {
				show_admin_region_tile($ID, $x, $y, $resources, $terrain);
			} else {
				show_region_tile($ID, $x, $y, $resources, $terrain);
			}

		// If at the end of a row, close the row
		if ($x == 0) { ?>
			</div><!-- .row -->
		<?php 
		}
	}
}

function show_admin_region_tile($ID, $x, $y, $resources, $terrain) { 

	$key = $x.','.$y;

	$tile_terrain = get_post_meta($ID, $x.','.$y.'-terrain', true);
	$tile_resources = array();
	foreach ($resources as $resource => $values) { 
		$tile_resources[$resource] = 'data-'.$resource.'="'.get_post_meta($ID, $key.'-'.$resource, true).'" ';
	}
	?>
	<div data-x="<?= $x; ?>" data-y="<?= $y; ?>" class="tile" data-terrain="<?= $tile_terrain; ?>" <?php foreach ($tile_resources as $output) { echo $output; } ?>>
		<div class="fields">
			<div class="alignleft">
				<?php
				foreach ($terrain as $terrain) { 
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
				foreach ($resources as $resource => $values) { 
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

function show_region_tile($ID, $x, $y, $resources, $terrain) { 
	$tile_terrain = get_post_meta($ID, $x.','.$y.'-terrain', true);
	?>
	<div data-x="<?= $x; ?>" data-y="<?= $y; ?>" class="tile" data-terrain="<?= $tile_terrain; ?>"></div>
<?php }

function build_region($ID) {
	foreach ($_POST as $key => $value) {
		if ($value) {
			update_post_meta($ID, $key, $value);
		}
	}
}

?>