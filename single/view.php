<?php
$view = $_GET['view'];

// Viewing trade routes
if ($view == 'trade') {

	// Must be the governor of the city or an admin
	if (is_user_logged_in() && $current_user->ID == get_the_author_meta('ID') || current_user_can('switch_themes')) { ?>

		<div id="alert" class="trade">
			<h2><?php the_title(); ?> - Trade Routes</h2>
			
		</div>

	<?php 
	// If the viewer is NOT the city governor (i.e. tryin' ta cheat)
	} else { ?>
	<div id="alert">
		<h2>No peekin'</h2>
		<p>The trade administrators of <?php the_title(); ?> aren't so keen on letting just any Joe, Jane, or <?php echo $current_user->display_name; ?> read all about the city's trade routes without the proper authorization.</p>
	</div>
	<?php }

// Viewing resources
} elseif ($view == 'resources') {

	// Must be the governor of the city or an admin
	if (is_user_logged_in() && $current_user->ID == get_the_author_meta('ID') || current_user_can('switch_themes')) { 

		// Get resource info
		include ( MAIN . 'resources.php');
		$list = array(); // create an empty array that will store the resources
		$count = 0;
		foreach ($resources as $key=>$resource) {
			if (get_post_meta($ID, $key, true) > 0) { 
				$count++; // If resource is present, increment the count
			} else {
				unset($resources[$key]); // If resource is not present, remove it from our list
			}
		}
		$i = 0;
		foreach ($resources as $key=>$resource) {
			if ($i == $count - 1) {
				$joiner = '.';
			} elseif ($i == $count - 2) {
				$joiner = ', and ';
			} else {
				$joiner = ', ';
			}
	    	if ($value < 4) {
	    		array_push($list, 'scarce amounts of <strong>'.$key.'</strong>'.$joiner);
	    	} elseif ($value >= 4 && $value < 7) {
	    		array_push($list, 'medium amounts of <strong>'.$key.'</strong>'.$joiner);
	    	} elseif ($value >= 7) {
	    		array_push($list, 'large amounts of <strong>'.$key.'</strong>'.$joiner);
	    	}
	    	$i++;
	    }
	    $list = implode($list);
		?>

		<div id="alert" class="resources">
			<h2><?php the_title(); ?></h2>

			<h3>Natural Resources</h3>
			<p>This site contains <?php echo $list; ?></p>
			<?php
			// Reset resources array
			include ( MAIN . 'resources.php');
			$i = 0;
			foreach ($resources as $key=>$resource) {

				$miners = get_post_meta($ID, $resource[1].'s', true);
				$name = ucfirst(substr($resource[0], 0, -6)); // remove '_stock'
				$type = get_post_meta($ID, $key, true);
				$stockpile = get_post_meta($ID, $resource[0], true) ? th(get_post_meta($ID, $resource[0], true)) : 0;
				if ($miners > 0) {
					$funding = get_post_meta($ID, $resource[1].'-funding', true);
					switch ($funding) {
						case 'bad':
			    			$mining = $miners * floor(0.5 * $type);
			    			break;
			    		case 'fair':
			    			$mining = $miners * $type;
			    			break;
			    		case 'good':
			    			$mining = $miners * floor(1.5 * $type);
			    			break;
			    		case 'excellent':
			    			$mining = $miners * floor(2 * $type);
			    			break;
					}
					$mining = '<small>(+'.$mining.')</small>'; // wrap up nicely to present
				}
				// First one? Then open the <ul>
				if ($i == 0) { ?>
					<ul class="resource-list clearfix">
						<li><?php 
							echo $name.': '.th($stockpile).' '.$mining; 
							if ($name == 'Food') {
								echo ' <small>(-'.$cost_food.')</small>';
							} elseif ($name == 'Fish') {
								echo ' <small>(-'.$cost_fish.')</small>';
							}
							?>
						</li>
				<?php 
				// Last one? Then close the </ul>
				} elseif ($i == count($resources) - 1) { ?>
						<li><?php 
							echo $name.': '.th($stockpile).' '.$mining; 
							if ($name == 'Food') {
								echo ' <small>(-'.$cost_food.')</small>';
							} elseif ($name == 'Fish') {
								echo ' <small>(-'.$cost_fish.')</small>';
							}
							?>
						</li>
					</ul>
				<?php } else { ?>
						<li><?php 
							echo $name.': '.th($stockpile).' '.$mining; 
							if ($name == 'Food') {
								echo ' <small>(-'.$cost_food.')</small>';
							} elseif ($name == 'Fish') {
								echo ' <small>(-'.$cost_fish.')</small>';
							}
							?>
						</li>
				<?php }
				$i++;
				unset($mining);
			}
			?>

			<h3>Trade Routes</h3>
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
					<small>(Pop: <?php echo th(get_post_meta($trades[$i], 'population', true)); ?>) - <?php echo get_user_by('id', get_post($trades[$i])->post_author)->display_name; ?></small>	
				</div>		
			<?php } 
			?>
			<p class="helper">To cancel selected trade routes, press Cancel below.</p>
			<input class="button helper" type="submit" id="cancel" name="cancel" value="Cancel" />
			</form>
		</div>

		<script>
			jQuery(document).ready(function($){
				var tradeForm = $('#alert form');
				tradeForm.find('input').change(function(){
					if ($('input:checked').length > 0) {
						$('.helper').show();
					} else {
						$('.helper').hide();
					}
				});
			});
		</script>

	<?php }
}
?>