<?php
header('Content-type: application/json');

$user = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));

$cities = new WP_Query(array(
	'author' => $user->ID,
	'posts_per_page' => -1
	)
);

$snapshot = array(
	'ID' => $user->ID,
	'cash' => intval(get_user_meta($user->ID, 'cash', true)),
	'cities' => array()
);

if ($cities->have_posts()) : while ($cities->have_posts()) : $cities->the_post();
$ID = get_the_ID();
array_push($snapshot['cities'], array(
	'ID' => intval($ID),
	'name' => get_the_title(),
	'region' => intval(get_post_meta($ID, 'region', true)),
	'population' => intval(get_post_meta($ID, 'population', true))
	)
);

endwhile;
endif;
wp_reset_postdata();

echo json_encode($snapshot);
