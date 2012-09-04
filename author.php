<?php get_header(); 
include ('structures.php');

// Get user info
$user = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));

// Get the ID
$id = $user->ID; 

// Get cash
$cash = get_field('cash','user_'.$id);
?>

<h1><?php echo $user->nickname; ?></h1>
<p>Cash: <?php echo $cash; ?></p>
<p>Cities: <?php echo count_user_posts($id); ?></p>
	<ul>
	<?php 
	$u_query = new WP_Query('posts_per_page=-1&author='.$id);
	while ($u_query->have_posts()) : $u_query->the_post(); ?>
		<li><?php the_title(); ?> (Pop: <?php $pop = get_field('population'); echo th($pop); ?>)</li>
	<?php endwhile; rewind_posts(); ?>
	</ul>

<?php 
global $current_user;
get_currentuserinfo();	

// Is the logged in user looking at their own profile?
if ($current_user->ID == $user->ID) { ?>
	<p>Hey there!</p>
	<p>This is your profile page. Soon there will be ways for you to change your password, color, etc.</p>
	<h2>Budget projections:</h2>
	<div id="budget">
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
	</div><!-- #budget -->
<?php } 
wp_reset_postdata(); 

get_footer(); ?>