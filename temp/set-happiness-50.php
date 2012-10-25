<!DOCTYPE html>
<html>
<?php
/*
Template Name: Set Happiness to 50
*/

$args = array(
	'posts_per_page' => -1,
);
$geo_query = new WP_Query($args); 

while ($geo_query->have_posts()) : $geo_query->the_post();

update_post_meta(get_the_ID(), 'happiness', 50);

endwhile;
wp_reset_postdata();

$alert = '<p>All happiness set to 50. Back to <a href="'.home_url().'">main map</a>.</p>';
?>

<body>
	<?php if ($alert) { ?>
		<div id="alert"><?php echo $alert; ?></div>
	<?php } ?>	
</body>

</html>