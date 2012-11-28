<!DOCTYPE html>
<html>
<?php
/*
Template Name: Set # of Ports to 0
*/

$args = array(
	'posts_per_page' => -1,
);
$geo_query = new WP_Query($args); 

while ($geo_query->have_posts()) : $geo_query->the_post();

update_post_meta(get_the_ID(), 'ports', 0);

endwhile;
wp_reset_postdata();

$alert = '<p>All cities have 0 ports. Back to <a href="'.home_url().'">main map</a>.</p>';
?>

<body>
	<?php if ($alert) { ?>
		<div id="alert"><?php echo $alert; ?></div>
	<?php } ?>	
</body>

</html>