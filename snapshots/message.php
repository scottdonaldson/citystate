<?php 
header('Content-type: application/json');
$ID = $post->ID;

$snapshot = array(
	'from' => intval(get_post_meta($ID, 'from', true)),
	'to' => intval(get_post_meta($ID, 'to', true)),

	'subject' => get_the_title(),
	'content' => get_the_content()
);

echo json_encode($snapshot);