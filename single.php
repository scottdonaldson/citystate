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
include 'structures-repeat.php';

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
		foreach($structures as $structure=>$cost) {
			$loc_x_[$structure] = get_post_meta($post->ID, $structure.'-x', true);
			$loc_y_[$structure] = get_post_meta($post->ID, $structure.'-y', true);
			if ($loc_x_[$structure] == $x && $loc_y_[$structure] == $y) {
				echo $structure.' structure no-build';
			}
		} 
		foreach ($repeatables as $repeat=>$cost) {
			$total = get_post_meta($post->ID, $repeat.'s', true);
			for ($i = 1; $i <= $total; $i++) {
				$x_[$repeat] = get_post_meta($post->ID, $repeat.'-'.$i.'-x', true);
				$y_[$repeat] = get_post_meta($post->ID, $repeat.'-'.$i.'-y', true);
				if ($x_[$repeat] == $x && $y_[$repeat] == $y) {
					echo $repeat.' structure no-build';
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
			} 
			foreach ($repeatables as $repeat=>$cost) { ?>
				<input data-cost="<?php echo $cost; ?>" data-repeat="true" id="<?php echo $repeat; ?>" name="structure" type="radio" value="<?php echo $repeat; ?>" />
				<label><?php echo $repeat.' ('.$cost.')'; ?></label>
			<?php }
			?>
			<input id="x" name="x" type="hidden" />
			<input id="y" name="y" type="hidden" />
			<input id="repeat" name="repeat" type="hidden" />	
			<input id="structure-cost" name="structure-cost" type="hidden" />
			<input type="submit" value="build" name="update" />
		</form>
	</div>

	<?php } ?>

</div><!-- #map -->

<?php get_footer(); ?>