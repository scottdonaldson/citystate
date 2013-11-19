<?php 
header('Content-type: application/json');
$ID = get_the_ID();

$snapshot = array(
	// Name
	'name' => get_the_title(),
	'id' => $ID,
	'slug' => $post->post_name,
	'tiles' => array(),
	'position' => array(),
	'cities' => array()
);

for ($x = 1; $x <= 10; $x++) {
	array_push($snapshot['tiles'], array());
	for ($y = 1; $y <= 10; $y++) {
		array_push( $snapshot['tiles'][$x - 1], get_post_meta($ID, $x.','.$y.'-terrain', true) );
	}
}

$snapshot['position']['x'] = intval(get_post_meta($ID, 'POS-x', true));
$snapshot['position']['y'] = intval(get_post_meta($ID, 'POS-y', true));

$cities = new WP_Query(array(
	'posts_per_page' => -1,
	'meta_key' => 'region',
	'meta_value' => $ID
	)
);

$city = 0;
if ($cities->have_posts()) : while ($cities->have_posts()) : $cities->the_post();
	$ID = get_the_ID();
	$snapshot['cities'][$city] = array(
		'name' => get_the_title(),
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
				array_push($snapshot['cities'][$city]['structures'], array(
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
wp_reset_postdata();

echo json_encode($snapshot);