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
			echo '<p>Cash: <span id="user-cash">'.th(get_field('cash', 'user_'.$current_user->ID)).'</span> (<a href="'.site_url().'/budget">View Budget</a>)</p>';
			$turns = get_field('turns', 'user_'.$current_user->ID);
			if ($turns > 1) {
				echo '<p><a href="'.site_url().'/docket">'.$turns.' orders of business</a> on your docket.</p>';
			} elseif ($turns > 0) {
				echo '<p><a href="'.site_url().'/docket">'.$turns.' order of business</a> on your docket.</p>';
			} else {
				echo '<p>No remaining orders of business on your docket.</p>';
			}
			echo '<p><a href="'.wp_logout_url( home_url() ).'">Log out &raquo;</a></p>'; ?>
		</div>

		<?php if (is_single()) { ?>

			<div class="module">
				<?php if (get_the_author_meta('ID') == $current_user->ID) { ?>
					<p>Your City: <strong><?php the_title(); ?></strong></p>
					<p>Population: <?php $pop = th(get_post_meta($post->ID, 'population', true)); echo $pop; ?></p>
					<?php $happiness = get_post_meta($post->ID, 'happiness', true); 
						if ($happiness < 5) {
							$happy = 'fleeing';
							$message = 'People are fleeing the city in anger!';
						} elseif ($happiness < 10) {
							$happy = 'extremely_unhappy';
							$message = 'Extremely unhappy';
						} elseif ($happiness < 20) {
							$happy = 'very_unhappy';
							$message = 'Very unhappy';
						} elseif ($happiness < 45) {
							$happy = 'unhappy';
							$message = 'Unhappy';
						} elseif ($happiness < 55) {
							$happy = 'neutral';
							$message = 'Neither happy nor unhappy';
						} elseif ($happiness < 80) {
							$happy = 'happy';
							$message = 'Happy';
						} elseif ($happiness < 90) {
							$happy = 'very_happy';
							$message = 'Very happy';
						} elseif ($happiness < 95) {
							$happy = 'extremely_happy';
							$message = 'Extremely happy';
						} else {
							$happy = 'flocking';
							$message = 'People from all over flock to this city!';
						} ?>
					<small class="face <?php echo $happy; ?>"><?php echo $message; ?></small>
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

		<div class="module">
			<p>You aren't logged in or you don't have an account. Want to <?php wp_register(); ?>?</p>
		</div>

	<?php } ?>
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