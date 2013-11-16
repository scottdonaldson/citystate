<?php 
global $current_user;
get_currentuserinfo();
?>

</div><!-- #main -->
<div id="toolbar" class="clearfix">

	<?php 
	// breadcrumbs navigation (literally)
	// for links to world/region maps
	breadcrumbs($post->ID); 
	
	if (is_user_logged_in()) { 
		show_user_module($current_user); 
	
		if (is_category()) {
			show_keys_module($current_user);
		}
		
		if (is_single() && get_post_type($post->ID) == 'post') { 
			if (get_the_author_meta('ID') == $current_user->ID) {
				show_user_city_module($post->ID);
			} else {
				show_not_user_city_module($post->ID);
			}
		} 

	// if user is not logged in
	} else { 
		show_not_logged_in_module();
	} ?>
	
	<div class="nav">
		<?php wp_nav_menu('primary'); ?>
	</div>

</div>

<div id="version">
	<?php $theme = wp_get_theme();
	echo $theme->Version;
	?>
</div>

<script src="<?php echo bloginfo('template_url'); ?>/js/plugins.js"></script>
<script src="<?php echo bloginfo('template_url'); ?>/js/graphics.js"></script>
<!-- <script src="<?php echo bloginfo('template_url'); ?>/js/script.js"></script> -->

<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-9215814-12']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>

<?php wp_footer(); ?>

</body>
</html>