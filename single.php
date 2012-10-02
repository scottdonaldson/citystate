<?php get_header(); the_post(); 

if ($_GET['visit'] == 'first') {
	echo '<div id="alert">Welcome to your new city!</div>';
}

if ($_GET['meta'] == 'true') {
	$meta = get_post_custom();
	echo '<div id="alert"><pre>';
	the_meta(); 
	echo '</pre></div>';
}

// Get main map
include 'maps/originalia.php';

// Get all structures
include 'structures.php';

// Get user info
global $current_user;
get_currentuserinfo();
?>

<div id="map" class="clearfix">

	<?php foreach ( range(1,100) as $tile ) { ?>

	<div 
		data-x="<?php $x = fmod($tile, 10); if ($x != 0) { echo $x; } else { echo 10; } ?>" 
		data-y="<?php $y = ceil($tile/10); echo $y; ?>" 
		class="tile <?php 
		foreach($structures as $structure=>$values) {
			$values[] = $values;

			// Non-repeating 
			if ($values[0] == false) {
				$loc_x_[$structure] = get_post_meta($post->ID, $structure.'-x', true);
				$loc_y_[$structure] = get_post_meta($post->ID, $structure.'-y', true);
				if ($loc_x_[$structure] == $x && $loc_y_[$structure] == $y) {
					echo $structure.' structure no-build';
				}
			// Repeating
			} elseif ($values[0] == true) {
				$total = get_post_meta($post->ID, $structure.'s', true);
				for ($i = 1; $i <= $total; $i++) {
					$x_[$structure] = get_post_meta($post->ID, $structure.'-'.$i.'-x', true);
					$y_[$structure] = get_post_meta($post->ID, $structure.'-'.$i.'-y', true);
					if ($x_[$structure] == $x && $y_[$structure] == $y) {
						echo $structure.' structure no-build';
					}
				}
			}
		} ?>"
		<?php foreach($structures as $structure=>$values) {
			$values[] = $values;
			if ($values[0] == false) {
				$loc_x_[$structure] = get_post_meta($post->ID, $structure.'-x', true);
				$loc_y_[$structure] = get_post_meta($post->ID, $structure.'-y', true);
				if ($loc_x_[$structure] == $x && $loc_y_[$structure] == $y) {
					echo 'data-structure="'.$structure.'"';
				}
			} elseif ($values[0] == true) {
				$total = get_post_meta($post->ID, $structure.'s', true);
				for ($i = 1; $i <= $total; $i++) {
					$x_[$structure] = get_post_meta($post->ID, $structure.'-'.$i.'-x', true);
					$y_[$structure] = get_post_meta($post->ID, $structure.'-'.$i.'-y', true);
					if ($x_[$structure] == $x && $y_[$structure] == $y) {
						echo 'data-structure="'.$structure.'"';
						echo 'data-id="'.$i.'"';
					}
				}
			}
		} ?>>
	</div>

	<?php } 

	// Make sure the user is logged in
	// and built this city to be able to modify
	if (is_user_logged_in() && ($current_user->ID == get_the_author_meta('ID'))) { ?>

	<div><!-- wrapping build and demolish in one div -->
		<div id="build" class="infobox">
			<h2>Build:</h2>
			<form method="post" action="<?php echo get_permalink().'?structure=build'; ?>">
				<?php 
				foreach ($structures as $structure=>$values) { 
					$values[] = $values;
					// Non-repeating structures
					if ($values[0] == false) {	
						// Get location
						$x = get_post_meta($post->ID, $structure.'-x', true);
						$y = get_post_meta($post->ID, $structure.'-y', true);		

						// Only show build option if structure is not yet built
						if ($x == '0' && $y == '0') { ?>
							<input id="<?php echo $structure; ?>" name="structure" type="radio" value="<?php echo $structure; ?>" />
							<label><?php echo $structure.' ('.$values[1].')'; ?></label>
						<?php 
						}
					// Repeating structures	
					} elseif ($values[0] == true) { ?>
						<input id="<?php echo $structure; ?>" name="structure" type="radio" value="<?php echo $structure; ?>" />
						<label><?php echo $structure.' ('.$values[1].')'; ?></label>
					<?php 
					}
				} ?>
				<input id="x" name="x" type="hidden" />
				<input id="y" name="y" type="hidden" />	
				<input type="submit" value="build" name="update" />
			</form>
		</div>
		<div id="demolish" class="infobox">
			<h2>Demolish</h2>
			<form method="post" action="<?php echo get_permalink().'?structure=demolish'; ?>">
				<input type="checkbox" name="demolish">
				<label>Do you want to demolish this structure? (50)</label>
				<input type="hidden" id="demo-structure" name="demo-structure" />
				<input id="demo-x" name="demo-x" type="hidden" />
				<input id="demo-y" name="demo-y" type="hidden" />
				<input id="id" name="id" type="hidden" />
				<input type="submit" value="demolish" name="update" />
			</form>
		</div>
	</div>

	<?php } ?>

</div><!-- #map -->

<?php get_footer(); ?>