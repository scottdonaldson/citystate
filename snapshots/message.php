<?php 
header('Content-type: application/json');
$ID = $post->ID;

$snapshot = array(
	// Name
	'name' => get_the_title(),
	'from' => get_post_meta($ID, 'from', true),
	'to' => get_post_meta($ID, 'to', true),

	'subject' => get_the_title(),
	'content' => get_the_content()
);

echo json_encode($snapshot);