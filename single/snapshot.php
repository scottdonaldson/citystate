<div id="snapshot" class="hidden">
	<div class="snap-content">

	<?php // Public: ?>

	<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
	<div class="location">
		<?php
		$region = get_the_category();
		$x = get_post_meta($ID, 'location-x', true);
		$y = get_post_meta($ID, 'location-y', true); ?>
		<a class="marker" href="<?php the_permalink(); ?>" data-region="<?php echo $region[0]->slug; ?>" data-x="<?php echo $x; ?>" data-y="<?php echo $y; ?>"></a>
	</div>
	<p class="population">Population: <?php $pop = get_post_meta($ID, 'population', true); echo th($pop); ?></p>

	<?php // Private:
	if (is_user_logged_in() && $current_user->ID == get_the_author_meta('ID')) { ?>
		<?php 
		$happiness = get_post_meta($post->ID, 'happiness', true); 
			if ($happiness < 5) {
				$happy = 'fleeing';
				$message = 'People are fleeing the city in anger!';
			} elseif ($happiness < 10) {
				$happy = 'extremely_unhappy';
				$message = 'Extremely unhappy';
			} elseif ($happiness < 20) {
				$happy = 'very_unhappy';
				$message = 'Very unhappy';
			} elseif ($happiness < 45) {
				$happy = 'unhappy';
				$message = 'Unhappy';
			} elseif ($happiness < 55) {
				$happy = 'neutral';
				$message = 'Neither happy nor unhappy';
			} elseif ($happiness < 80) {
				$happy = 'happy';
				$message = 'Happy';
			} elseif ($happiness < 90) {
				$happy = 'very_happy';
				$message = 'Very happy';
			} elseif ($happiness < 95) {
				$happy = 'extremely_happy';
				$message = 'Extremely happy';
			} else {
				$happy = 'flocking';
				$message = 'People from all over flock to this city!';
			} ?>
		<p class="face <?php echo $happy; ?>"><?php echo $message; ?></p>
		<?php
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
		foreach ($resources as $resource => $value) {
	    	if ($value < 4) {
	    		array_push($list, '<li>Scarce amounts of '.$resource.'</li>');
	    	} elseif ($value >= 4 && $value < 7) {
	    		array_push($list, '<li>Medium amounts of '.$resource.'</li>');
	    	} elseif ($value >= 7) {
	    		array_push($list, '<li>Large amounts of '.$resource.'</li>');
	    	}
	    	$i++;
	    } 
	    $list = implode($list);

	    // Reset all resources
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
		<p>Resources</p>
		<ul><?php echo ucfirst($list); ?></ul>
	<?php } ?>
	
	</div><!-- .snap-content -->
</div>