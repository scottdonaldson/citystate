<?php get_header(); 

// If logged in, get current user info
global $current_user;
get_currentuserinfo();

// Retrieve all the cities
query_posts('posts_per_page=-1'); while (have_posts()) : the_post(); 
	$ID = get_the_ID();
	$loc = get_field('location');
	$city_[$ID] = '<div id="city-'.$ID.'" class="city '.$loc.'"></div>'; ?>

<?php endwhile; rewind_posts(); ?>

<div id="map" class="clearfix">

	<?php foreach (range(1,100) as $tile) { ?>

		<div 
			data-x="<?php $x = fmod($tile, 10); if ($x != 0) { echo $x; } else { echo 10; } ?>" 
			data-y="<?php $y = ceil($tile/10); echo $y; ?>"
			class="tile <?php 

				if (
					( ($x==1||$x==7||$x==8||$x==9||$x==0) && $y==1) || 
					( ($x==1||$x==2||$x==9||$x==0) && $y==2) ||
					( ($x==9||$x==0) && $y==3) ||
					( ($x==0) && $y==4) ||
					( ($x==1||$x==2||$x==3) && $y==5) ||
					( ($x==1||$x==2||$x==3||$x==4) && $y==6) ||
					( ($x==1||$x==2) && $y==7) ||
					( ($x==3||$x==8||$x==9||$x==0) && $y==8) ||
					( ($x==3||$x==4||$x==9||$x==0) && $y==9) ||
					( ($x==1||$x==0) && $y==10)
				) { echo 'water'; }

			?>">
			<?php while (have_posts()) : the_post(); 

			// Is there a city here?
			if (get_field('location-x') == $x && get_field('location-y') == $y) { 

				// Get city info
				include ('structures.php');
				$non = 0;
				$repeaters = 0;
				foreach($structures as $structure=>$values) {
					$values[] = $values;

					// Count non-repeaters
					if ($values[0] == false) {
						$count = get_post_meta(get_the_ID(), $structure.'-x', true);
						if ($count != 0) { $non++; }

					// Count repeaters
					} elseif ($values[0] == true) {
						$count = get_post_meta(get_the_ID(), $structure.'s', true);
						$repeaters = $repeaters + $count;
					}
				} ?>

				<div id="city-<?php the_ID(); ?>" class="city
					<?php 
					echo ' repeaters:'.$repeaters.' ';
					if ($repeaters <= 5) { echo 'r00'; 
					} elseif ($repeaters > 5 && $repeaters <= 10) { echo 'r01';
					} elseif ($repeaters > 10 && $repeaters <= 15) { echo 'r02';
					} elseif ($repeaters > 15 && $repeaters <= 20) { echo 'r03';
					} elseif ($repeaters > 20) { echo 'r04'; 
					}
					if ($non == 1 ) { echo ' n01'; 
					} elseif ($non ==2 ) { echo ' n02'; 
					} elseif ($non == 3 ) { echo ' n03'; }
					
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
	
	<?php } unset($quadrant); ?>

	<?php if (is_user_logged_in()) { ?>
	<div id="build" class="infobox">
		<h2>Build city</h2>
		<form action="<?php echo site_url(); ?>/build" method="post">
			<input id="cityName" name="cityName" type="text" />
			<input id="x" name="x" type="hidden" />
			<input id="y" name="y" type="hidden" />			
			<input type="submit" id="buildCity" name="buildCity" value="build city" />
		</form>
	</div>
	<?php } ?>

</div><!-- #map -->

<?php wp_reset_query(); ?>

<?php get_footer(); ?>