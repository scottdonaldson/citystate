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
$region = get_query_var('category_name');

// Retrieve cities in this region.
$city_query = new WP_Query(array(
	'category_name' => $region,
	'posts_per_page' => -1,
	'order' => 'ASC'
	)
);

$cities = array(); // empty array to hold cities
$trade_partners = array(); // empty array to hold ALL user trade partners

while ($city_query->have_posts()) : $city_query->the_post();
	$ID = get_the_ID();
	$x = get_post_meta($ID, 'location-x', true);
	$y = get_post_meta($ID, 'location-y', true);

	// For the current user's cities, set 'current' to true
	// and check to see if there are trade partners to add to array
	if (get_the_author_meta('ID') == $current_user->ID) {
		array_push($cities, array('ID' => $ID, 'x' => $x, 'y' => $y, 'current' => true));
		
		if (count(get_post_meta($ID, 'trade')) > 0) {
			array_push($trade_partners, get_post_meta($ID, 'trade', true));
		}
	// For other users' cities, 'current' is false
	} else {
		array_push($cities, array('ID' => $ID, 'x' => $x, 'y' => $y, 'current' => false));
	}
	
endwhile;
wp_reset_postdata();

// Get number of regions user has scouted
$scouted = get_user_meta($current_user->ID, 'scouted', true);
$scouted_here = false;
?>

<div id="map" class="clearfix">

	<?php 
	include( MAIN .'maps/'.$region.'.php'); 
	foreach ($map as $row => $tiles) {
		$x = 0; 
		foreach ($tiles as $tile) { 
			$x++;
			$y = $row; 
			$type = $tile[0]; ?>

		<?php 
		// Wrap rows in a div
		if ($x == 1) { ?>
		<div class="row row-<?php echo $y; ?> clearfix">
		<?php } ?>	

		<div 
			data-x="<?php echo $x; ?>" 
			data-y="<?php echo $row; ?>"
			data-terrain="<?php echo $type; ?>"
			class="tile <?php 

				if ($type == 'water') { 
					echo 'water'; 
				} else { 
					echo 'land '.$type; 
				}

				// If user is scouting the territory, highlight it
				for ($s = 0; $s < $scouted; $s++) {
					if (get_user_meta($current_user->ID, 'show_scouted', true) == 'show' &&
						get_user_meta($current_user->ID, 'scouted_'.$s.'-region', true) == $region &&
						get_user_meta($current_user->ID, 'scouted_'.$s.'-x', true) == $x &&
						get_user_meta($current_user->ID, 'scouted_'.$s.'-y', true) == $y) {
							echo ' scouted';
							$scouted_here = true;
							break;
					}
				}
				// "Plain old" highlighting
				if (isset($_GET['x']) && isset($_GET['y'])) {
					if ($x == $_GET['x'] && $y == $_GET['y']) {
						echo ' highlighted';
					}
				}

				// City?
				foreach ($cities as $city) {
					if ($x == $city['x'] && $y == $city['y']) {
						echo ' city';
						break;
					}
				}

			?>">
			<?php 
			foreach ($cities as $city) {
				if ($x == $city['x'] && $y == $city['y']) {
					$ID = $city['ID'];
					$c = get_post($ID); // the post object

					// Get city info
					include ('structures.php');
					$non = 0;
					$repeaters = 0;
					foreach($structures as $structure=>$values) {
						include( MAIN .'structures/values.php');

						// Count non-repeaters
						if ($max != 0) {
							$count = get_post_meta($ID, $structure.'-y', true);
							if ($count != 0) { $non++; }

						// Count repeaters
						} else {
							$count = get_post_meta($ID, $structure.'s', true);
							$repeaters = $repeaters + $count;
						}
					} ?>

					<div id="city-<?php echo $ID; ?>" class="city
						<?php 
						// User's city?
						if ($city['current'] == true) {
							echo ' user-city';
						// Trade partner?
							// Must not be current user
							// and city ID must be in array of current user's cities' trade partners
						} elseif (in_array($ID, $trade_partners)) {
							echo ' trade-partner';
						}

						// Displaying the size of the city
						if ($repeaters <= 5) { echo ' r00'; 
						} elseif ($repeaters > 5 && $repeaters <= 10) { echo ' r01';
						} elseif ($repeaters > 10 && $repeaters <= 15) { echo ' r02';
						} elseif ($repeaters > 15 && $repeaters <= 20) { echo ' r03';
						} elseif ($repeaters > 20) { echo ' r04'; 
						}
						if ($non == 1 ) { echo ' n01'; 
						} elseif ($non == 2 ) { echo ' n02'; 
						} elseif ($non >= 3 ) { echo ' n03'; }
						
						?>">
						<a class="marker" href="<?php echo $c->guid; ?>"></a>
					</div><!-- .city -->	
					
					<div class="info">
						<h2><?php echo $c->post_title; ?></h2>
						<small>
							<?php echo get_user_by('id', $c->post_author)->display_name; ?>
						</small>
						<ul>
							<li>Pop: <?php echo th(get_post_meta($ID, 'population', true)); ?></li>
						</ul>
					</div><!-- .info -->

					<?php 
				} // end if to check if there's a city here
			} // end foreach 
			// Show info for scouting, if there's no city here
			if ($scouted_here == true && !($x == $city['x'] && $y == $city['y'])) { ?>
				<div class="info">
					<ul>
					<?php
					$resources = $map[$y][$x - 1][1];
					foreach ($resources as $key=>$value) {
				    	if ($value < 4) {
				    		echo '<li>Scarce amounts of <strong>'.$key.'</strong></li>';
				    	} elseif ($value >= 4 && $value < 7) {
				    		echo '<li>Medium amounts of <strong>'.$key.'</strong></li>';
				    	} elseif ($value >= 7) {
				    		echo '<li>Large amounts of <strong>'.$key.'</strong></li>';
				    	}
				    }
				    ?>
					</ul>
				</div>

			<?php $scouted_here = false;
			} ?>
		</div><!-- .tile -->	
	
	<?php } ?>
		<?php 
	// End of row
	if ($x == 10) { ?>
	</div>
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

					echo $tile[0];

				?>">
				</div>
			<?php } 
			} ?>
		</a>

	<?php }
	}

	if (is_user_logged_in()) { 
		// Get user info to determine cost of building a new city
		$cities = count_user_posts($current_user->ID); ?>
		<div id="build" class="infobox">
			<p><span class="terrain"></span> at (<span class="x"></span>,&nbsp;<span class="y"></span>):</p>

			<form action="<?php echo site_url(); ?>/build" method="POST">
				<p>Build a city:</p>
				<input id="cityName" name="cityName" type="text" maxlength="25" />
				<input id="x" name="x" type="hidden" />
				<input id="y" name="y" type="hidden" />	
				<input id="region_id" name="region_id" value="<?php echo get_query_var('cat'); ?>" type="hidden" />	
				<input id="region_slug" name="region_slug" value="<?php echo $region; ?>" type="hidden" />	
				<input class="button" type="submit" id="buildCity" name="buildCity" value="Build City (<?php echo th(1500*$cities + 500); ?>)" />
			</form>
			<?php 
			// If scouts are already out in the territory, can't send more
			if (get_user_meta($current_user->ID, 'scouting', true) != 'yes') { ?>
			<small>or</small>
			<form action="<?php echo home_url().'/'.$region.'/'; ?>" method="POST">
				<input id="scout-x" name="scout-x" type="hidden">
				<input id="scout-y" name="scout-y" type="hidden">
				<input id="scout-region" name="scout-region" value="<?php echo $region; ?>" type="hidden">
				<input class="button" type="submit" id="scout" name="scout" value="Scout Territory (350)">
			</form>
			<?php } ?>
		</div>
	<?php } 

	?>

</div><!-- #map -->

<?php wp_reset_query(); ?>

<?php get_footer(); ?>