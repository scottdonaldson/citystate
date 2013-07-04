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
			if (current_user_can('switch_themes') && $_GET['edit'] == 'true') {
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

function show_region_overlays($resources) { ?>
	<h2>Data overlays:</h2>
	<ul id="overlays">
		<?php
		foreach ($resources as $resource => $values) { ?>
			<li data-overlay="<?= $resource; ?>"><?= ucfirst($resource); ?></li>
		<?php } ?>
		<li data-overlay="remove">Remove overlay</li>
	</ul>
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

function show_region_neighbors($geo, $ID, $resources, $terrain) {
	$x = get_post_meta($ID, 'POS-x', true);
	$y = get_post_meta($ID, 'POS-y', true);

	$neighbor_query = new WP_Query(array(
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
	while ($neighbor_query->have_posts()) : $neighbor_query->the_post(); 
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
				<?php show_region_map($N_ID, $resources, $terrain); ?>
			</a>
		<?php
		// }
	?>
	<?php
	endwhile;
	wp_reset_postdata();

	foreach ($geo as $cardinal) { 
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

// Show region admin function -- note that this is different from the "admin"
// above, as this is for any user who is logged in, to allow them to build
function show_region_admin($user, $ID) {
	
}

?>