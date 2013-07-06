<?php 
// The main map.

get_header(); ?>

<div id="map">
	<?php 
	// TODO: this
	show_world_map(); ?>
	<a href="<?php echo home_url(); ?>/originalia" title="Originalia">
		<img src="<?php echo bloginfo('template_url'); ?>/images/maps/originalia.png" />
	</a>

	<a href="<?php echo home_url(); ?>/secondo-1" title="Secondo">
		<img src="<?php echo bloginfo('template_url'); ?>/images/maps/secondo-1.png" />
	</a>

	<a href="<?php echo home_url(); ?>/secondo-2" title="Secondo">
		<img src="<?php echo bloginfo('template_url'); ?>/images/maps/secondo-2.png" />
	</a>

	<a style="clear: left; margin-left: 60px;" href="<?php echo home_url(); ?>/secondo-3" title="Secondo">
		<img src="<?php echo bloginfo('template_url'); ?>/images/maps/secondo-3.png" />
	</a>

	<a href="<?php echo home_url(); ?>/secondo-4" title="Secondo">
		<img src="<?php echo bloginfo('template_url'); ?>/images/maps/secondo-4.png" />
	</a>
</div><!-- #map -->

<?php get_footer(); ?>