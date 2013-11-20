<?php 
// The main map.
function main_map() { ?>
	<script>var world = {"tiles":[["water","grass","water","water","hills","hills","hills","grass","water","grass"],["grass","grass","hills","sand","sand","sand","grass","grass","water","water"],["sand","forest","mountains","mountains","water","water","grass","grass","grass","water"],["sand","forest","mountains","mountains","hills","water","grass","sand","sand","water"],["grass","grass","hills","water","hills","sand","grass","sand","sand","sand"],["water","sand","hills","water","water","grass","grass","sand","grass","sand"],["water","sand","grass","water","forest","grass","grass","grass","water","sand"],["sand","grass","forest","forest","sand","forest","grass","water","water","water"],["sand","sand","forest","forest","sand","water","water","water","water","mountains"],["water","sand","sand","grass","sand","water","water","sand","water","hills"]]}</script>
	<script src="<?= bloginfo('template_url'); ?>/js/worldmap.js"></script>
	<?php
	$cities = new WP_Query(array(
		'posts_per_page' => -1,
		'meta_key' => 'region',
		'meta_value' => $ID
		)
	);
	$city = 0;
	$citiesArray = [];
	if ($cities->have_posts()) : while ($cities->have_posts()) : $cities->the_post();
		global $post;
		$ID = get_the_ID();
		$citiesArray[$city] = array(
			'name' => get_the_title(),
			'slug' => $post->post_name,
			'id' => $ID,
			'x' => intval(get_post_meta($ID, 'location-x', true)),
			'y' => intval(get_post_meta($ID, 'location-y', true)),
			'author' => get_the_author(),
			'author_id' => get_the_author_meta('ID'),
			'population' => get_post_meta($ID, 'population', true) ? get_post_meta($ID, 'population', true) : 0,
			'structures' => array()
		);
		foreach (get_structures() as $slug => $structure) {
			if (get_post_meta($ID, $slug.'-number', true)) {
				
				for ($i = 1; $i <= get_post_meta($ID, $slug.'-number', true); $i++) {
					array_push($citiesArray[$city]['structures'], array(
						'x' => intval(get_post_meta($ID, $slug.'-'.$i.'-x', true)),
						'y' => intval(get_post_meta($ID, $slug.'-'.$i.'-y', true)),
						'size' => $slug === 'stadium' ? 2 : 1
						)
					);
				}
			}
		}
		$city++;
	endwhile;
	endif;
	wp_reset_postdata(); ?>
	<script>var cities = <?= json_encode($citiesArray); ?></script>
	<?php 
	}
add_action('wp_head', 'main_map');
get_header(); ?>

<svg id="map" onload="showWorldMap()"></svg>

<?php get_footer(); ?>