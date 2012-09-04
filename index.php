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
				$pop = get_field('population'); ?>

				<div id="city-<?php the_ID(); ?>" class="city
					<?php 
					if ($pop < 100) { echo 'tiny'; 
					} elseif ($pop >= 100 && $pop < 500) { echo 'small';
					} elseif ($pop >= 500 && $pop < 1000) { echo 'med';
					} elseif ($pop >= 1000) { echo 'large'; }
					?>">
					<a class="marker <?php $login = get_the_author_meta('user_login'); echo 'user-'.$login; ?>" href="<?php the_permalink(); ?>"></a>
				</div><!-- .city -->	
				
				<div class="info">
					<h2 class="city-name"><?php the_title(); ?></h2>
					<small class="city-builder"><?php the_author(); ?></small>
					<ul>
						<li>Pop: <?php echo th($pop); ?></li>
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