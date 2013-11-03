<?php 
header('Content-type: application/json');
$ID = $post->ID;

$snapshot = array(
	// Name
	'name' => get_the_title(),
	'tiles' => array(),
	'cities' => array()
);

for ($x = 1; $x <= 10; $x++) {
	array_push($snapshot['tiles'], array());
	for ($y = 1; $y <= 10; $y++) {
		// array_push( $snapshot['tiles'][$x - 1], get_post_meta($ID, $x.','.$y.'-terrain', true) );
	}
}

$cities = new WP_Query(array(
	'posts_per_page' => -1,
	'meta_key' => 'region',
	'meta_value' => $ID
	)
);
$i = 0;
if ($cities-> have_posts()) : while ($cities->have_posts()) : the_post();
	array_push( $snapshot['cities'][$i], get_post_custom());
	$i++;
endwhile;
endif;
wp_reset_postdata();

echo json_encode($snapshot);