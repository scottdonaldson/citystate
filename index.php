<?php 
// The main map.
function get_world_map() { 
	$regions = new WP_Query(array(
		'post_type' => 'region'
		)
	);
	?>
	<script>var regions = <?= json_encode($regions->posts); ?>;</script>
	<script src="<?= bloginfo('template_url'); ?>/js/worldmap.js"></script>
<?php } add_action('wp_head', 'get_world_map');
get_header(); ?>

<div id="map"></div><!-- #map -->

<script>show_world_map();</script>

<?php get_footer(); ?>