<?php 
add_action('wp_head', 'farb_css');
add_action('wp_head', 'farb_js');
function farb_css() {
	$url = get_bloginfo('template_url');
    echo '<link rel="stylesheet" href="'.$url.'/plugins/farb/farbtastic.css" />';
}
function farb_js() {
	$url = get_bloginfo('template_url');
    echo '<script src="'.$url.'/plugins/farb/farbtastic.js"></script>'; ?>
    <script>
    jQuery(document).ready(function($){

		var colorInput = $('#color'),
			color = colorInput.val();
		colorInput.css({
			'background': color,
			'color': 'transparent',
		});
		$('#colorpicker').farbtastic(colorInput);
    });
    </script>
<?php }

get_header(); 
include ('structures.php');

// Get user info
$user = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));

// Get the ID
$id = $user->ID; 

// Get cash
$cash = get_field('cash','user_'.$id);
?>

<div class="container">
	<div class="module">
		<h1 class="header active"><?php echo $user->nickname; ?></h1>
		<div class="content visible">
			<p>Cash: <?php echo th($cash); ?></p>
			<p>Cities: <?php echo count_user_posts($id); ?></p>
				<ul>
				<?php 
				$u_query = new WP_Query('posts_per_page=-1&author='.$id);
				while ($u_query->have_posts()) : $u_query->the_post(); ?>
					<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> (Pop: <?php $pop = get_field('population'); echo th($pop); ?>)</li>
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
			<div class="content">
				<p>Select the parts of your profile you want to update.</p>
				<form id="profile" action="?profile=updated" method="post">
					<section class="name">
						<h3>Name</h3>
						<?php 
						// If 0, then the user has NOT changed his/her name
						if (get_field('name_change', 'user_'.$current_user->ID) == 0) { ?>
							<small>You can't change your username (what you use to log in with), but you can change the way your name is displayed on scoreboards and the like. You can only change your name once!</small>
							<input type="text" maxlength="25" id="displayName" name="displayName" placeholder="<?php echo $current_user->display_name; ?>" />
							<small><em>*Name changes will appear after logging out and in again.</em></small>
						<?php } else { ?>
							<small>You've already changed your name! Doing it more than once would be confusing for everyone. If you still want to change your name, you could post on <a href="http://www.reddit.com/r/citystate" target="_blank">Reddit</a> and hope that you're answered.</small>
						<?php } ?>
					</section>
					<section class="color">
						<h3>Color</h3>
						<input type="text" id="color" name="color" value="<?php the_field('color','user_'.$user->ID); ?>" />
						<div id="colorpicker"></div>
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
			            <input type="submit" value="Update Profile" name="submit" />
			        </p>
			    </form>
			</div><!-- .content -->
		</div><!-- .module -->

		<div class="module">
			<h2 class="header">Budget projections</h2>
			<div class="content clearfix" id="budget">
				<div class="first"><strong>City name</strong></div>
				<div class="second"><strong>Tax revenue</strong></div>
				<div class="third"><strong>Upkeep costs</strong></div>
				
				<?php
				while ($u_query->have_posts()) : $u_query->the_post(); 
					$post_id = get_the_ID();
					$pop = get_post_meta(get_the_ID(), 'population', true);
					echo '<div class="first city">'.get_the_title().'</div>';

					// Taxes
					$taxes = floor(0.05*$pop);
					echo '<div class="second taxes">'.th($taxes).'</div>';

					// Upkeep costs
					$upkeep = array();
					foreach ($structures as $structure=>$values) {
						include( MAIN .'structures/values.php');

						// Only non-repeating for now
						if ($max != 0) {
							$loc_x_[$structure] = get_post_meta($post_id, $structure.'-x', true);
							$loc_y_[$structure] = get_post_meta($post_id, $structure.'-y', true);

							if ( !($loc_x_[$structure] == 0 && $loc_y_[$structure] == 0) ) {
								
								// Upkeep costs (.02*cost)	
								array_push($upkeep,-(0.02*$cost));
							}
						}
					}
					echo '<div class="third upkeep">'.array_sum($upkeep).'</div>';
				endwhile; ?>

				<div class="first city"><strong>Total</strong></div>
				<div class="second total-taxes"><strong>Loading...</strong></div>
				<div class="third total-upkeep"><strong>Loading...</strong></div>

				<div class="first"><strong>Grand total</strong></div>
				<div class="grand"><strong>Loading...</strong></div>
			</div><!-- .content #budget -->
		</div><!-- .module -->
<?php } 
wp_reset_postdata(); ?>

</div><!-- .container -->

<?php get_footer(); ?>