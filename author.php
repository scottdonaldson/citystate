<?php 
add_action('wp_head', 'farb_css');
add_action('wp_head', 'farb_js');
function farb_css() {
	$url = get_bloginfo('template_url');
    echo '<link rel="stylesheet" href="'.$url.'/plugins/farb/farbtastic.css" />';
}
function farb_js() {
	$url = get_bloginfo('template_url');
    echo '<script src="'.$url.'/plugins/farb/farbtastic.js"></script>';
}

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
			<p>Cash: <?php echo $cash; ?></p>
			<p>Cities: <?php echo count_user_posts($id); ?></p>
				<ul>
				<?php 
				$u_query = new WP_Query('posts_per_page=-1&author='.$id);
				while ($u_query->have_posts()) : $u_query->the_post(); ?>
					<li><?php the_title(); ?> (Pop: <?php $pop = get_field('population'); echo th($pop); ?>)</li>
				<?php endwhile; rewind_posts(); ?>
				</ul>
		</div><!-- .content -->
	</div><!-- .module -->		

	<?php 
	global $current_user;
	get_currentuserinfo();	

	// Is the logged in user looking at their own profile?
	if ($current_user->ID == $user->ID) { ?>
	
		<div class="module">	
			<h2 class="header">Profile</h2>
			<div class="content">
				<form id="profile" action="?profile=updated" method="post">
					<h3>Color</h3>
					<div class="color">
						<input type="text" id="color" name="color" value="<?php the_field('color','user_'.$user->ID); ?>" />
						<div id="colorpicker"></div>
					</div>
					<h3>Password</h3>
					<div class="pass1">
				        <input type="password" name="pass1" id="pass1" size="16" value="" autocomplete="off" />
				        <span class="description"><?php _e("If you would like to change the password type a new one. Otherwise leave this blank."); ?></span>
				    </div>
				    <div class="pass2">
			            <input type="password" name="pass2" id="pass2" size="16" value="" autocomplete="off" />
			            <span class="description"><?php _e("Type your new password again."); ?></span>
			        </div>
			        <p class="submit">
			            <input type="hidden" name="user_login" id="user_login" value="<?php echo $user->user_login; ?>" />
			            <input type="submit" class="button-primary" value="Update Profile" name="submit" />
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
					$pop = get_field('population');
					echo '<div class="first city">'.get_the_title().'</div>';

					// Taxes
					$taxes = floor(0.05*$pop);
					echo '<div class="second taxes">'.th($taxes).'</div>';

					// Upkeep costs
					$upkeep = array();
					foreach ($structures as $structure=>$values) {
						$values[] = $values;

						// Only non-repeating for now
						if ($values[0] == false) {
							$loc_x_[$structure] = get_post_meta($post_id, $structure.'-x');
							$loc_y_[$structure] = get_post_meta($post_id, $structure.'-y');

							if ( !($loc_x_[$structure][0] == 0 && $loc_y_[$structure][0] == 0) ) {
								
								// Upkeep costs (.02*cost)	
								array_push($upkeep,-(0.02*$values[1]));
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