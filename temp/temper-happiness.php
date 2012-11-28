<!DOCTYPE html>
<html>
<?php
/*
Template Name: Temper Happiness
*/

$args = array(
	'posts_per_page' => -1,
);
$geo_query = new WP_Query($args); 

while ($geo_query->have_posts()) : $geo_query->the_post();

	$ID = get_the_ID();
	$happiness = get_post_meta($ID, 'happiness', true);
	$new_happy = round(0.333*($happiness + 100), 3);

	// Weighted average between old happiness and 50
	update_post_meta($ID, 'happiness', $new_happy);

endwhile;
wp_reset_postdata();

$alert = '<p>Happiness of all cities has been tempered. Back to <a href="'.home_url().'">main map</a>.</p>';
?>

<body>
	<?php if ($alert) { ?>
		<div id="alert"><?php echo $alert; ?></div>
	<?php } ?>	
</body>

</html>