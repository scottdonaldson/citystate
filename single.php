<?php get_header(); the_post(); 

// Get all structures
include ( MAIN . 'structures.php');

// Get user info
global $current_user;
get_currentuserinfo();

// Get city ID
$ID = get_the_ID();

// Set initial resource costs equal to 0
include ( MAIN . 'resources.php');
foreach ($resources as $key=>$resource) {
	$name = substr($resource[0], 0, -6); // remove '_stock'
	$cost_[$name] = 0;
}

if (isset($_GET['visit']) && $_GET['visit'] == 'first') {
	echo '<div id="alert"><h2>Welcome to your new city!</h2></div>';
}
?>

<?php 
// The snapshot (used in AJAX calls)
include( MAIN . 'single/snapshot.php'); ?>

<div id="map" class="clearfix <?php if (is_user_logged_in() && $current_user->ID == get_the_author_meta('ID')) { echo 'user-city'; } else { echo 'not-user-city'; } ?>">

	<?php foreach ( range(1,100) as $tile ) { 
		$x = fmod($tile, 10);
		$y = ceil($tile/10);
		?>

	<?php 
	// Wrap rows in a div
	if ($x == 1) { ?>
	<div class="row row-<?php echo $y; ?> clearfix">
	<?php } ?>
	<div 
		data-x="<?php echo $x; ?>" 
		data-y="<?php echo $y; ?>" 
		class="tile <?php 
		foreach($structures as $structure=>$values) {
			include( MAIN .'structures/values.php');

			// Non-repeating 
			if ($max == 1) {
				$x_[$structure] = get_post_meta($ID, $structure.'-x', true);
				$y_[$structure] = get_post_meta($ID, $structure.'-y', true);
				if ($x_[$structure] == $x && $y_[$structure] == $y) {
					echo $structure.' structure no-build';
				}
			// Repeating
			} else {
				$total = get_post_meta($ID, $structure.'s', true);
				for ($i = 1; $i <= $total; $i++) {
					$x_[$structure] = get_post_meta($ID, $structure.'-'.$i.'-x', true);
					$y_[$structure] = get_post_meta($ID, $structure.'-'.$i.'-y', true);
					if ($x_[$structure] == $x && $y_[$structure] == $y) {
						echo $structure.' structure no-build';
					}
				}
			}
		} ?>"
		<?php foreach($structures as $structure=>$values) {
			include( MAIN .'structures/values.php');

			if ($max == 1) {
				$loc_x_[$structure] = get_post_meta($ID, $structure.'-x', true);
				$loc_y_[$structure] = get_post_meta($ID, $structure.'-y', true);
				if ($loc_x_[$structure] == $x && $loc_y_[$structure] == $y) {
					echo 'data-structure="'.$structure.'"';
					// Upgradeable if the level is less than max for upgrades
					if ($upgrade > 0) {
						// Cost to upgrade
						echo 'data-cost="'.$cost.'" data-upgrade="true"';
						echo 'data-level="'.get_post_meta($ID, $structure.'-level', true).'"';
					} elseif (get_post_meta($ID, $structure.'-level', true) == $upgrade) {
						echo 'data-level="'.get_post_meta($ID, $structure.'-'.$i.'-level', true).'"';
					}
				}
			} else {
				$total = get_post_meta($ID, $structure.'s', true);
				for ($i = 1; $i <= $total; $i++) {
					$x_[$structure] = get_post_meta($ID, $structure.'-'.$i.'-x', true);
					$y_[$structure] = get_post_meta($ID, $structure.'-'.$i.'-y', true);
					$level = get_post_meta($ID, $structure.'-'.$i.'-level', true);
					if ($x_[$structure] == $x && $y_[$structure] == $y) {
						echo 'data-structure="'.$structure.'"';
						echo 'data-id="'.$i.'"';
						
						// Upgradeable if the level is less than max for upgrades
						if ($upgrade > 0 && $level < $upgrade) {
							echo 'data-cost="'.$cost.'" data-upgrade="true"';
							echo 'data-level="'.$level.'"';
						} elseif (get_post_meta($ID, $structure.'-'.$i.'-level', true) == $upgrade) {
							echo 'data-level="'.$level.'"';
						}

						// Neighborhoods cost resources (used in single/view.php)
						if ($structure == 'neighborhood' && ($level == 1 || $level == 2) ) {
							// Once-upgraded even costs 1 food
							if ($level == 1 && $i%2 == 0) {
								$cost_food = $cost_food + 1;
							// Once-upgraded odd costs 1 fish
							} elseif ($level == 1 && $i%2 == 1) {
								$cost_fish = $cost_fish + 1;
							// Twice-upgraded costs 1 food and 1 fish
							} else {
								$cost_food = $cost_food + 1;
								$cost_fish = $cost_fish + 1;
							}
						}
					}
				}
			}
		} ?>>
	
	</div>
	<?php 
	// End of row
	if ($x == 0) { ?>
	</div>
	<?php } ?>

	<?php } 

	$geo = array('nw', 'n', 'ne', 'w', 'e', 'sw', 's', 'se'); 
	foreach ($geo as $cardinal) { 
		$terrain = get_post_meta(get_the_ID(), 'map-'.$cardinal, true);
		?>

		<div id="<?php echo $cardinal; ?>" class="terrain <?php echo $terrain; ?>"></div>

	<?php }

	// Make sure the user is logged in
	// and built this city to be able to modify
	if (is_user_logged_in() && ($current_user->ID == get_the_author_meta('ID'))) { ?>

	<div><!-- wrapping build and demolish in one div -->
		<div id="build" class="infobox">
			<p>Build a structure at (<span class="x"></span>,&nbsp;<span class="y"></span>):</p>
			<form method="post" action="<?php echo get_permalink().'?structure=build'; ?>">
				<?php 
				// Create an empty resource structures array to populate with both
				// structures and (preceding them) the corresponding keys. Use this later.
				$resource_structures = [];
				foreach ($resources as $key=>$resource) {
					array_push($resource_structures, $key, $resource[1]);
				}
				// Open the list
				echo '<ul>';
				foreach ($structures as $structure=>$values) { 
					include( MAIN .'structures/values.php');

					// Non-repeating structures
					if ($max == 1) {	
						// Get location
						$y = get_post_meta($ID, $structure.'-y', true);		

						// Only show build option if structure is not yet built
						// and if has passed 1/2 of population at which it is desired
						if ($y == '0' && $pop >= 0.5*$desired) { ?>
						
							<li id="<?php echo $structure; ?>">
								<?php echo ucwords($name).' ('.th($cost).')'; ?>
							</li>
						<?php 
						}
						
					// Repeating structures
					} else { 
						// Only show if max is 0 (can build as many as desired)
						// or if the count is less than the maximum allowed
						// AND if has passed 1/2 of population at which it is desired
						if (($max == 0 || get_post_meta($ID, $structure.'s', true) < $max) && $pop >= 0.5*$desired) { 
							// Resource-related structures. Now we use the above $resource_structures array.
							if (in_array($name, $resource_structures)) {
								// Complicated, but this is how we test if there is resource present in city
								if (get_post_meta($ID, $resource_structures[array_search($name, $resource_structures) - 1], true) > 0 && $pop >= 1000) { ?>
									<li id="<?php echo $structure; ?>">
										<?php echo ucwords($name).' ('.th($cost).')'; ?>
									</li>
								<?php
								}
							} else { ?>
								<li id="<?php echo $structure; ?>">
									<?php echo ucwords($name).' ('.th($cost).')'; ?>
								</li>
							<?php 
							}
						}
					} // end repeating structures
				} // end foreach
				echo '</ul>';
				?>
				<input id="build-structure" name="build-structure" type="hidden" />
				<input id="build-x" name="build-x" type="hidden" />
				<input id="build-y" name="build-y" type="hidden" />	
				<p class="helper"></p>
				<input class="button" type="submit" value="build" name="update" />
			</form>
		</div>
		<div id="extra" class="infobox">
			<p><span class="name-structure"></span> at (<span class="x"></span>,&nbsp;<span class="y"></span>):</p>
			<form class="upgrade" method="post" action="<?php echo get_permalink().'?structure=upgrade'; ?>">
				<input type="hidden" id="upgrade-structure" name="upgrade-structure" />
				<input id="upgrade-x" name="upgrade-x" type="hidden" />
				<input id="upgrade-y" name="upgrade-y" type="hidden" />
				<input id="upgrade-id" name="upgrade-id" type="hidden" />
				<input class="button" type="submit" value="Upgrade" name="update" />
			</form>
			<form method="post" action="<?php echo get_permalink().'?structure=demolish'; ?>">
				<input type="hidden" id="demo-structure" name="demo-structure" />
				<input id="demo-x" name="demo-x" type="hidden" />
				<input id="demo-y" name="demo-y" type="hidden" />
				<input id="demo-id" name="demo-id" type="hidden" />
				<p class="helper"></p>
				<input class="button" type="submit" value="Demolish (50)" name="update" />
			</form>
		</div>
	</div>

	<?php } ?>

</div><!-- #map -->

<?php
// If user is viewing something in the city
// (Pull this after rendering the map so that we can use its data)
if (isset($_GET['view'])) { include( MAIN . 'single/view.php'); }
?>

<?php get_footer(); ?>