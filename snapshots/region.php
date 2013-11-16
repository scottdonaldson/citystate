<?php 
header('Content-type: application/json');
$ID = get_the_ID();

$snapshot = array(
	// Name
	'name' => get_the_title(),
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

if ($cities->have_posts()) : while ($cities->have_posts()) : $cities->the_post();
	array_push( $snapshot['cities'], array(
		'name' => get_the_title(),
		'id' => get_the_ID(),
		'author' => get_the_author(),
		'author_id' => get_the_author_meta('ID'),
		'population' => get_post_meta(get_the_ID(), 'population', true) ? get_post_meta(get_the_ID(), 'population', true) : 0
		)
	);
endwhile;
endif;
wp_reset_postdata();

echo json_encode($snapshot);