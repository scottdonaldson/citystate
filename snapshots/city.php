<?php 
header('Content-type: application/json');
$ID = $post->ID;

$snapshot = array(
	// Name
	'name' => get_the_title(),
	// Location
	'x' => intval(get_post_meta($ID, 'location-x', true)),
	'y' => intval(get_post_meta($ID, 'location-y', true)),

	'population' => intval(get_post_meta($ID, 'population', true)),
	'happiness' => intval(get_post_meta($ID, 'happiness', true)),
	'education' => intval(get_post_meta($ID, 'education', true)),
	'culture' => intval(get_post_meta($ID, 'culture', true)),

	// Structures
	'structures' => array(),

	// Tiles (for which have structures)
	'tiles' => array()
);

foreach (get_structures() as $slug => $structure) {
	if (get_post_meta($ID, $slug.'-number', true)) {
		$snapshot['structures'][$structure['slug']] = array();
		for ($i = 1; $i <= get_post_meta($ID, $slug.'-number', true); $i++) {
			array_push($snapshot['structures'][$structure['slug']], array(
				'x' => intval(get_post_meta($ID, $slug.'-'.$i.'-x', true)),
				'y' => intval(get_post_meta($ID, $slug.'-'.$i.'-y', true)),
				'level' => intval(get_post_meta($ID, $slug.'-'.$i.'-level', true))
				)
			);
			array_push(
				$snapshot['tiles'], 
				get_post_meta($ID, $slug.'-'.$i.'-x', true) . ', ' . get_post_meta($ID, $slug.'-'.$i.'-y', true)
			);
		}
	}
}

echo json_encode($snapshot);