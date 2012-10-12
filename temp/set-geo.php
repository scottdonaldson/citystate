<!DOCTYPE html>
<html>
<?php
/*
Template Name: Set Geographic Coordinates
*/

$args = array(
	'posts_per_page' => -1,
);
$geo_query = new WP_Query($args); 

// Including map for Originalia right here
$map = array(
	/* 
	row number => array(value for each tile: 0 water, 1 land)
	 */
	1  => array(0, 1, 1, 1, 1, 1, 0, 0, 0, 0),
	2  => array(0, 0, 1, 1, 1, 1, 1, 1, 0, 0),
	3  => array(1, 1, 1, 1, 1, 1, 1, 1, 0, 0),
	4  => array(1, 1, 1, 1, 1, 1, 1, 1, 1, 0),
	5  => array(0, 0, 0, 1, 1, 1, 1, 1, 1, 1),
	6  => array(0, 0, 0, 0, 1, 1, 1, 1, 1, 1),
	7  => array(0, 0, 1, 1, 1, 1, 1, 1, 1, 1),
	8  => array(1, 1, 0, 1, 1, 1, 1, 0, 0, 0),
	9  => array(1, 1, 0, 0, 1, 1, 1, 1, 0, 0),
	10 => array(0, 1, 1, 1, 1, 1, 1, 1, 1, 0),
);

while ($geo_query->have_posts()) : $geo_query->the_post();

// Set geographic characteristics
	$geo = array('nw', 'n', 'ne', 'w', 'e', 'sw', 's', 'se');
	$x = get_post_meta(get_the_ID(), 'location-x', true);
	$y = get_post_meta(get_the_ID(), 'location-y', true);

	foreach ($geo as $cardinal) {
		// Find $map_x and $map_y on the map and set value (land or water)
		if ($cardinal == 'nw') {
			$map_x = $x - 1; $map_y = $y - 1;
		} elseif ($cardinal == 'n') {
			$map_x = $x; $map_y = $y - 1;
		} elseif ($cardinal == 'ne') {
			$map_x = $x + 1; $map_y = $y - 1;
		} elseif ($cardinal == 'w') {
			$map_x = $x - 1; $map_y = $y;
		} elseif ($cardinal == 'e') {
			$map_x = $x + 1; $map_y = $y;	
		} elseif ($cardinal == 'sw') {
			$map_x = $x - 1; $map_y = $y + 1;
		} elseif ($cardinal == 's') {
			$map_x = $x; $map_y = $y + 1;	
		} elseif ($cardinal == 'se') {
			$map_x = $x + 1; $map_y = $y + 1;	
		}		

		$val = $map[$map_y][$map_x - 1];
		if ($val == 0) {
			$val = 'water';
		} elseif ($val == 1) {
			$val = 'land';
		} else { $val = $val; }

		update_post_meta(get_the_ID(), 'map-'.$cardinal, $val);
	}
endwhile;
wp_reset_postdata();

$alert = '<p>Geographic coordinates set. Back to <a href="'.home_url().'">main map</a>.</p>';
?>

<body>
	<?php if ($alert) { ?>
		<div id="alert"><?php echo $alert; ?></div>
	<?php } ?>	
</body>

</html>