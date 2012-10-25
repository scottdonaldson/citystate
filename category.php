<?php 
add_action('wp_footer', 'cityname_validate');
function cityname_validate() {
	$url = get_bloginfo('template_url');
    echo '<script src="'.$url.'/plugins/validation/cityname.js"></script>'; ?>
<?php }
get_header(); 

// If logged in, get current user info
global $current_user;
get_currentuserinfo();

// Get region slug
$region_slug = get_query_var('category_name');

// Retrieve all the cities
query_posts('posts_per_page=-1&category_name='.$region_slug); while (have_posts()) : the_post(); 
	$ID = get_the_ID();
	$loc = get_field('location');
	$city_[$ID] = '<div id="city-'.$ID.'" class="city '.$loc.'"></div>'; ?>

<?php endwhile; rewind_posts(); ?>

<div id="map" class="clearfix">

	<?php 
	include( MAIN .'maps/'.$region_slug.'.php'); 
	foreach ($map as $row => $tiles) {
		$x = 0; 
		foreach ($tiles as $tile) { 
			$x++;
			$y = $row; ?>

		<div 
			data-x="<?php echo $x; ?>" 
			data-y="<?php echo $row; ?>"
			class="tile <?php 

				if ($tile == 0) { echo 'water'; }

			?>">
			<?php while (have_posts()) : the_post(); 

			// Is there a city here?
			if (get_field('location-x') == $x && get_field('location-y') == $y) { 

				// Get city info
				include ('structures.php');
				$non = 0;
				$repeaters = 0;
				foreach($structures as $structure=>$values) {
					include( MAIN .'structures/values.php');

					// Count non-repeaters
					if ($max != 0) {
						$count = get_post_meta(get_the_ID(), $structure.'-y', true);
						if ($count != 0) { $non++; }

					// Count repeaters
					} else {
						$count = get_post_meta(get_the_ID(), $structure.'s', true);
						$repeaters = $repeaters + $count;
					}
				} ?>

				<div id="city-<?php the_ID(); ?>" class="city
					<?php 
					if ($repeaters <= 5) { echo 'r00'; 
					} elseif ($repeaters > 5 && $repeaters <= 10) { echo 'r01';
					} elseif ($repeaters > 10 && $repeaters <= 15) { echo 'r02';
					} elseif ($repeaters > 15 && $repeaters <= 20) { echo 'r03';
					} elseif ($repeaters > 20) { echo 'r04'; 
					}
					if ($non == 1 ) { echo ' n01'; 
					} elseif ($non == 2 ) { echo ' n02'; 
					} elseif ($non >= 3 ) { echo ' n03'; }
					
					$login = get_the_author_meta('user_login'); echo ' user-'.$login; ?>">
					<a class="marker" href="<?php the_permalink(); ?>"></a>
				</div><!-- .city -->	
				
				<div class="info">
					<h2 class="city-name"><?php the_title(); ?></h2>
					<small class="city-builder"><?php the_author(); ?></small>
					<ul>
						<li>Pop: <?php echo th(get_field('population')); ?></li>
					</ul>
				</div><!-- .info -->
			
			<?php }
			endwhile; ?>
		</div><!-- .tile -->	
	
	<?php } 
	} 

	foreach ($neighbors as $cardinal => $slug) { 
		if ($slug !== 0) { ?>

		<a class="map" href="<?php echo home_url().'/'.$slug; ?>" id="<?php echo $cardinal; ?>">
			<?php include( MAIN . 'maps/'.$slug.'.php'); 
			foreach ($map as $row => $tiles) { 
				$x = 0; 
				foreach ($tiles as $tile) { 
					$x++;
					$y = $row; ?>
				<div class="tile no-build <?php 

					if ($tile == 0) { echo 'water'; }

				?>">
				</div>
			<?php } 
			} ?>
		</a>

	<?php }
	}

	if (is_user_logged_in()) { 
			// Get user info to determine cost of building a new city
			global $current_user; get_currentuserinfo(); 
			$cities = count_user_posts($current_user->ID); ?>
		<div id="build" class="infobox">
			<p>Build city at (<span class="x"></span>,&nbsp;<span class="y"></span>)</p>
			<form action="<?php echo site_url(); ?>/build" method="post">
				<p>Name:</p>
				<input id="cityName" name="cityName" type="text" maxlength="25" />
				<input id="x" name="x" type="hidden" />
				<input id="y" name="y" type="hidden" />	
				<input id="region_id" name="region_id" value="<?php echo get_query_var('cat'); ?>" type="hidden" />	
				<input id="region_slug" name="region_slug" value="<?php echo $region_slug; ?>" type="hidden" />	
				<input type="submit" id="buildCity" name="buildCity" value="Build City (<?php echo th(1500*$cities + 500); ?>)" />
			</form>
		</div>
	<?php } 

	?>

</div><!-- #map -->

<?php wp_reset_query(); ?>

<?php get_footer(); ?>