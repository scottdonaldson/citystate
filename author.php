<?php 
if (isset($_GET['snapshot']) && $_GET['snapshot'] === 'true') {
	include ( MAIN . 'snapshots/user.php');
} else {
get_header(); 

include ( MAIN . 'structures.php');

// Get user info
$user = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));

// Get the ID
$id = $user->ID; 

// Get cash
$cash = get_user_meta($id, 'cash', true);
?>

<div class="container">
	<div class="module">
		<h1 class="header"><?php echo $user->display_name; ?></h1>
		<div class="content">
			<?php 
			$u_query = new WP_Query('posts_per_page=-1&author='.$id);
			$totalpop = 0;
			while ($u_query->have_posts()) : $u_query->the_post(); 
				$totalpop += get_post_meta(get_the_ID(), 'population', true);
			endwhile; rewind_posts(); ?>
			<p>Total population: <?php echo th($totalpop); ?></p>
			<p>Cash: <?php echo th($cash); ?></p>
			<p>Cities: <?php echo count_user_posts($user->ID); ?></p>
				<ul>
				<?php 
				while ($u_query->have_posts()) : $u_query->the_post(); ?>
					<li><a class="snapshot" href="<?php the_permalink(); ?>"><?php the_title(); ?></a> (Pop: <?php echo th(get_post_meta(get_the_ID(), 'population', true)); ?>)</li>
				<?php endwhile; rewind_posts(); ?>
				</ul>
		</div><!-- .content -->
	</div><!-- .module -->		

	<?php 
	global $current_user;
	get_currentuserinfo();	

	// Is the logged in user looking at their own profile?
	// (also admins)
	if ($current_user->ID == $user->ID || current_user_can('switch_themes')) { ?>
	
		<div class="module profile">	
			<h2 class="header">Profile</h2>
			<div class="content clearfix">
				<p>Select the parts of your profile you want to update.</p>
				<form id="profile" action="?profile=updated" method="post">
					<section class="name">
						<h3>Name</h3>
						<?php 
						// If 0, then the user has NOT changed his/her name
						if (get_user_meta($current_user->ID, 'name_change', true) == 0) { ?>
							<small>You can't change your username (what you use to log in with), but you can change the way your name is displayed on scoreboards and the like. You can only change your name once!</small>
							<input type="text" maxlength="25" id="displayName" name="displayName" placeholder="<?php echo $current_user->display_name; ?>" />
							<small><em>*Name changes will appear after logging out and in again.</em></small>
						<?php } else { ?>
							<small>You've already changed your name! Doing it more than once would be confusing for everyone. If you still want to change your name, you could post on <a href="http://www.reddit.com/r/citystate" target="_blank">Reddit</a> and hope that you're answered.</small>
						<?php } ?>
					</section>
					<section class="password">
						<h3>Password</h3>
						<div class="pass1">
					        <input type="password" name="pass1" id="pass1" />
					        <span class="description"><?php _e("If you would like to change the password type a new one. Otherwise leave this blank."); ?></span>
					    </div>
					    <div class="pass2">
				            <input type="password" name="pass2" id="pass2" />
				            <span class="description"><?php _e("Type your new password again."); ?></span>
				        </div>
				    </section>
				    <p class="submit">
			            <input type="hidden" name="user_login" id="user_login" value="<?php echo $user->user_login; ?>" />
			            <input class="button" type="submit" value="Update Profile" name="submit" />
			        </p>
			    </form>
			</div><!-- .content -->
		</div><!-- .module -->
<?php } 
wp_reset_postdata(); ?>

</div><!-- .container -->

<?php 
get_footer(); 
}
?>