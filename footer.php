<div id="toolbar">

	<?php if (!is_home()) { ?>
		<a class="return" href="<?php echo home_url(); ?>">Main Map</a>
	<?php } else { ?>
		<a class="return"></a>
	<?php }
	
	if (is_user_logged_in()) { ?>
		<?php if (is_single()) { ?>
			
		<?php } ?>

		<div class="user">
			<?php 
			global $current_user;
			get_currentuserinfo();

			echo '<p>'.$current_user->display_name.'</p>'; 
			echo '<p>Cash: '.get_field('cash', 'user_'.$current_user->ID).'</p>';
				if (current_user_can('import')) {
					include ('cheats/more-cash.php');
				}
			echo '<p><a href="'.wp_logout_url( home_url() ).'">Log out</a></p>'; ?>
		</div>

		<?php if (is_single()) { ?>

		<div class="city">
			<p>City: <?php the_title(); ?></p>
			<p>Pop: <?php the_field('population'); ?></p>
		</div>

		<?php }
	} else { ?>
		<div class="user">
			<form name="loginform" id="loginform" action="http://scottdonaldson.net/wp-login.php" method="post">
				<p>Username:&nbsp;
					<input type="text" name="log" id="user_login" class="input" value="" size="20" tabindex="10" /></label>
				</p>
				<p>Password:&nbsp;
					<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" tabindex="20" /></label>
				</p>
				<p class="forgetmenot"><label for="rememberme"><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90" /> Remember Me</label></p>
				<p class="submit">
					<input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="Log In" tabindex="100" />
					<input type="hidden" name="redirect_to" value="http://scottdonaldson.net/citystate/" />
					<input type="hidden" name="testcookie" value="1" />
				</p>
			</form>
		</div>

	<?php } ?>
	<div class="nav">
		<?php wp_nav_menu('primary'); ?>
	</div>

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