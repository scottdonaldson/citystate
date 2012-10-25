</div><!-- #main -->
<div id="toolbar">

	<div class="return">
	<?php if (is_category()) { ?>
		<a href="<?php echo home_url(); ?>">World Map &raquo;</a>
	<?php } elseif (is_single()) { 
		$region = get_the_category(); ?>
		<a href="<?php echo get_category_link($region[0]->term_id); ?>">Back to <?php echo $region[0]->cat_name; ?> &raquo;</a>
		<a href="<?php echo home_url(); ?>">World Map &raquo;</a>
	<?php } elseif (!is_home()) { ?>
		<a href="<?php echo home_url(); ?>">World Map &raquo;</a>
	<?php } else { } ?>
	</div>

	<?php 
	if (is_user_logged_in()) { ?>
		<div class="module">
			<?php 
			global $current_user;
			get_currentuserinfo();

			echo '<p><strong><a href="'.site_url().'/user/'.$current_user->user_login.'">'.$current_user->display_name.'</a></strong></p>'; 
			echo '<p>Cash: '.th(get_field('cash', 'user_'.$current_user->ID)).'</p>';
			echo '<p><a href="'.wp_logout_url( home_url() ).'">Log out</a></p>'; ?>
		</div>

		<?php if (is_single()) { ?>

			<div class="module">
				<?php if (get_the_author_meta('ID') == $current_user->ID) { ?>
					<p>Your City: <strong><?php the_title(); ?></strong></p>
					<p>Population: <?php $pop = th(get_post_meta($post->ID, 'population', true)); echo $pop; ?></p>
					<p>Happiness: <?php 
						$happiness = get_post_meta($post->ID, 'happiness', true); 
						if ($happiness < 5) {
							echo 'People are fleeing the city in anger!';
						} elseif ($happiness < 10) {
							echo 'Extremely unhappy';
						} elseif ($happiness < 20) {
							echo 'Very unhappy';
						} elseif ($happiness < 45) {
							echo 'Unhappy';
						} elseif ($happiness < 55) {
							echo 'Neither happy nor unhappy';
						} elseif ($happiness < 80) {
							echo 'Happy';
						} elseif ($happiness < 90) {
							echo 'Very happy';
						} elseif ($happiness < 95) {
							echo 'Extremely happy';
						} else {
							echo 'People from all over flock to this city!';
						} ?></p>
				<?php } else { ?>
					<p>City: <strong><?php the_title(); ?></strong></p>
					<p>Population: <?php $pop = th(get_post_meta($post->ID, 'population', true)); echo $pop; ?></p>
					<p>Governed by <a href="<?php echo site_url(); ?>/user/<?php echo get_the_author_meta('user_login'); ?>"><?php the_author(); ?></a></p>
				<?php } ?>
				
			</div>

		<?php } elseif (is_category()) { ?>

		<?php }
	} else { ?>
		<div class="module">
			<?php 
			wp_login_form(array(
  				'label_remember' => __( 'Remember me' ),
  				'label_log_in' => __( 'Sign In' ),
  				'redirect' => site_url().'?login=success',
				)
			);
			?>
		</div>

	<?php } ?>
	<div class="nav">
		<?php if (!is_user_logged_in()) { ?>
			<p class="create">You don't have an account. Want to <?php wp_register(); ?>?</p>
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