</div><!-- #main -->
<div id="toolbar">

	<?php if (!is_home()) { ?>
		<a class="return" href="<?php echo home_url(); ?>">Main Map</a>
	<?php } else { ?>
		<a class="return"></a>
	<?php }
	
	if (is_user_logged_in()) { ?>
		<div class="user">
			<?php 
			global $current_user;
			get_currentuserinfo();

			echo '<p><a href="'.site_url().'/user/'.$current_user->user_login.'">'.$current_user->display_name.'</a></p>'; 
			echo '<p>Cash: '.th(get_field('cash', 'user_'.$current_user->ID)).'</p>';
			echo '<p><a href="'.wp_logout_url( home_url() ).'">Log out</a></p>'; ?>
		</div>

		<?php if (is_single()) { ?>

		<div class="city">
			<p>City: <?php the_title(); ?></p>
			<p>Pop: <?php 
			$pop = th(get_field('population'));
			echo $pop; ?></p>
		</div>

		<?php }
	} else { ?>
		<div class="user">
			<?php 
			wp_login_form(array(
  				'label_remember' => __( 'Remember me' ),
  				'label_log_in' => __( 'Sign In' ),
				)
			);
			?>
		</div>

	<?php } ?>
	<div class="nav">
		<?php if (!is_user_logged_in()) { ?>
			<p class="create">You don't have an account. Want to <a href="<?php echo site_url(); ?>/create-account">create one</a>?</p>
		<?php }
		
		wp_nav_menu('primary'); ?>
	</div>

</div>

<div id="version">
	<?php $theme = wp_get_theme();
	echo $theme->Version;
	?>
</div>

<script src="<?php echo bloginfo('template_url'); ?>/js/plugins.js"></script>
<script src="<?php echo bloginfo('template_url'); ?>/js/script.js"></script>

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