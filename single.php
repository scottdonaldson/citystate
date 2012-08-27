<?php get_header(); the_post(); 

if (isset($_GET['visit']) && ($_GET['visit'] == 'first')) {
	echo '<div id="alert">Welcome to your new city!</div>';
}

if ($_GET['meta'] == 'true') {
	$meta = get_post_custom();
	echo '<div id="alert"><pre>';
	the_meta(); 
	echo '</pre></div>';
}

// Get all structures
include 'structures.php';

// Get user info
global $current_user;
get_currentuserinfo();
?>

<div id="map" class="draggable">

	<?php foreach ( range(1,100) as $tile ) { ?>

	<div 
		data-x="<?php $x = fmod($tile, 10); if ($x != 0) { echo $x; } else { echo 10; } ?>" 
		data-y="<?php $y = ceil($tile/10); echo $y; ?>" 
		class="tile <?php
		foreach($structures as $structure=>$cost) {
			if (get_field($structure)) {
				while (has_sub_field($structure)) {
					$location_x_[$structure] = get_sub_field('location-x');
					$location_y_[$structure] = get_sub_field('location-y');
					if ($location_x_[$structure] == $x && $location_y_[$structure] == $y) {
						echo $structure.' structure no-build';
					}
				}
			}
		} ?>">
	</div>

	<?php } 

	// Make sure the user is logged in
	// and built this city to be able to modify
	if (is_user_logged_in() && ($current_user->ID == get_the_author_meta('ID'))) { ?>

	<div id="build" class="infobox">
		<h2>Build:</h2>
		<form method="post" action="<?php echo get_permalink().'?build=structure'; ?>">
			<?php 
			foreach ($structures as $structure=>$cost) { 
				
				$x = get_post_meta($post->ID, $structure.'-x', true);
				$y = get_post_meta($post->ID, $structure.'-y', true);
						
				// Only show build option if structure is not yet built
				if ($x == '0' && $y == '0') { ?>
					<input data-cost="<?php echo $cost; ?>" id="<?php echo $structure; ?>" name="structure" type="radio" value="<?php echo $structure; ?>" />
					<label><?php echo $structure.' ('.$cost.')'; ?></label>
				<?php }
			} ?>
			<input id="x" name="x" type="hidden" />
			<input id="y" name="y" type="hidden" />	
			<input id="structure-cost" name="structure-cost" type="hidden" />
			<input type="submit" value="build" name="update" />
		</form>
	</div>

	<?php } ?>

</div><!-- #map -->

<?php get_footer(); ?>