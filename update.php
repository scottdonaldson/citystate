<?php
/*
Template Name: Update
*/
get_header(); 

if (isset($_POST['update'])) {
	if ($_POST['password'] == 'rundailyupdate') {

		echo '<div id="alert"><p>Daily update complete</p></div>';

		// Update all cities
		global $post;
		$posts = get_posts('numberposts=-1');
		foreach ($posts as $post) {
			// Get info
			setup_postdata($post);
			$ID = get_the_ID();
			$user_ID = get_the_author_meta('ID');

			// Update population
			$pop = get_field('population',$ID);
			update_field('population',floor($pop*1.1),$ID);

			// Taxes
			$cash = get_field('cash','user_'.$user_ID);
			$taxes = floor(0.05*$pop);
			update_field('cash', $cash+$taxes, 'user_'.$user_ID);

			// Structure-related
			include 'structures.php';
			foreach ($structures as $structure=>$cost) {

				// Make sure structure has been built, then continue
				if (get_field($structure)) {
					while (has_sub_field($structure)) {
						$location_x_[$structure] = get_sub_field('location-x');
						$location_y_[$structure] = get_sub_field('location-y');
						if ($location_x_[$structure] != '' && $location_y_[$structure] != '') {
							
							// Upkeep costs (.02*cost)	
							$cash = get_field('cash','user_'.$user_ID);
							update_field('cash', $cash-(0.02*$cost), 'user_'.$user_ID);

							// Each adds to population (.01*cost)
							$pop = get_field('population',$ID);
							update_field('population', $pop+.01*$cost, $ID);

						}
					}
				}
			}
		}
		wp_reset_postdata();

	} else {
		echo '<div id="alert">Bad password. No update for you.</div>';
	}
}

?>

<form method="post">
	<input type="password" id="password" name="password" />
	<input type="submit" id="update" name="update" value="run daily update" />
</form>

<?php get_footer(); ?>