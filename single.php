<?php get_header(); the_post(); 

// Get all structures
include ( MAIN . 'structures.php');

// Get user info
global $current_user;
get_currentuserinfo();

// Get city ID
$ID = get_the_ID();

if ($_GET['visit'] == 'first') {
	echo '<div id="alert"><h2>Welcome to your new city!</h2></div>';
}

// If user is viewing trade routes
if ($_GET['view'] == 'trade') {
	// Must be the governor of the city
	if ($current_user->ID == get_the_author_meta('ID')) { 
		// If user is canceling any trade routes
		if (isset($_POST['cancel'])) {
			$traderoutes = $_POST['traderoute'];

			// For each trade route being canceled...
			foreach ($traderoutes as $traderoute) {
				// Dump the route itself
				delete_post_meta($ID, 'trade', $traderoute); // This city with partner
				delete_post_meta($traderoute, 'trade', $ID); // Partner with city
				// Update number of routes in each city
				update_post_meta($ID, 'traderoutes', get_post_meta($ID, 'traderoutes', true) - 1);
				update_post_meta($traderoute, 'traderoutes', get_post_meta($traderoute, 'traderoutes', true) - 1);
			}
			
			// Display confirmation
			if (count($traderoutes) == 1) {
				$trade_update = '<p>1 route successfully canceled.</p>';
			} else {
				$trade_update = '<p>'.count($traderoutes).' routes successfully canceled.</p>';
			}

			// Send a message
			$notify = wp_insert_post(array(
				'post_type' => 'message',
				'post_title' => 'Trade route between '.get_post($traderoute)->post_title.' and '.get_post($ID)->post_title.' has been canceled',
				'post_content' => $current_user->display_name.' canceled the trade route between '.get_post($ID)->post_title.' and your city of '.get_post($traderoute)->post_title.'. This will take some getting used to.',
				'post_status' => 'publish'
				)
			);
			add_post_meta($notify, 'to', get_post($traderoute)->post_author);
			add_post_meta($notify, 'from', $current_user->ID);
			add_post_meta($notify, 'read', 'unread');

			// Now we update the target pop. values of each city (going down).
			// Decreases are based on 7.5% of partner's actual population
			$to_pop = floor(0.075 * get_post_meta($traderoute, 'population', true)); 
			$from_pop = floor(0.075 * get_post_meta($ID, 'population', true));
			$to_target_current = get_post_meta($traderoute, 'target-pop', true);
			$from_target_current = get_post_meta($ID, 'target-pop', true);
			update_post_meta($to_city, 'target-pop', $to_target_current - $from_pop);
			update_post_meta($from_city, 'target-pop', $from_target_current - $to_pop);
		}
		?>

		<div id="alert" class="trade">
			<h2><?php the_title(); ?> - Trade Routes</h2>
			<?php echo $trade_update; ?>
			<form action="<?php the_permalink(); ?>?view=trade" method="POST">
			<?php 
			$traderoutes = get_post_meta(get_the_ID(), 'traderoutes', true);
			$trades = get_post_meta(get_the_ID(), 'trade');
			for ($i = 0; $i < count($trades); $i++) { ?>
				<div class="clearfix">
				<input type="checkbox" name="traderoute[]" value="<?php echo $trades[$i]; ?>" />
					<a href="<?php echo get_permalink($trades[$i]); ?>" target="_blank">
						<?php echo get_post($trades[$i])->post_title; ?>
					</a>&nbsp;
					<small>(Pop: <?php echo th(get_post_meta($trades[$i], 'population', true)); ?>)</small>	
				</div>		
			<?php } 
			?>
			<p class="helper">To cancel selected trade routes, press Cancel below.</p>
			<input class="button helper" type="submit" id="cancel" name="cancel" value="Cancel" />
			</form>
		</div>

		<script>
			jQuery(document).ready(function($){
				var tradeForm = $('#alert.trade form');
				tradeForm.find('input').change(function(){
					if ($('input:checked').length > 0) {
						$('.helper').show();
					} else {
						$('.helper').hide();
					}
				});
			});
		</script>
	<?php 
	// If the viewer is NOT the city governor (i.e. tryin' ta cheat)
	} else { ?>
	<div id="alert">
		<h2>No peekin'.</h2>
		<p>The trade administrators of <?php the_title(); ?> aren't so keen on letting just any Joe, Jane, or <?php echo $current_user->display_name; ?> read all about the city's trade routes without the proper authorization.</p>
	</div>
	<?php }
}
?>

<div id="map" class="clearfix">

	<?php foreach ( range(1,100) as $tile ) { 
		$x = fmod($tile, 10);
		$x = $x == 0 ? 10 : $x;
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
					if ($x_[$structure] == $x && $y_[$structure] == $y) {
						echo 'data-structure="'.$structure.'"';
						echo 'data-id="'.$i.'"';
						
						// Upgradeable if the level is less than max for upgrades
						if ($upgrade > 0 && get_post_meta($ID, $structure.'-'.$i.'-level', true) < $upgrade) {
							echo 'data-cost="'.$cost.'" data-upgrade="true"';
							echo 'data-level="'.get_post_meta($ID, $structure.'-'.$i.'-level', true).'"';
						} elseif (get_post_meta($ID, $structure.'-'.$i.'-level', true) == $upgrade) {
							echo 'data-level="'.get_post_meta($ID, $structure.'-'.$i.'-level', true).'"';
						}
					}
				}
			}
		} ?>>
	
	</div>
	<?php 
	// End of row
	if ($x == 10) { ?>
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
				echo '<ul>';
				foreach ($structures as $structure=>$values) { 
					include( MAIN .'structures/values.php');

					// Non-repeating structures
					if ($max == 1) {	
						// Get location
						$y = get_post_meta($ID, $structure.'-y', true);		

						// Only show build option if structure is not yet built
						// and if has passed 1/2 of population at which it is desired
						if ($y == '0' && get_post_meta($ID, 'population', true) >= 0.5*$desired) { ?>
						
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
						if (($max == 0 || get_post_meta($ID, $structure.'s', true) < $max) && get_post_meta($ID, 'population', true) >= .5*$desired) { ?>
							<li id="<?php echo $structure; ?>">
								<?php echo ucwords($name).' ('.th($cost).')'; ?>
							</li>
						<?php 
						}
					}
				} 
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



<?php get_footer(); ?>