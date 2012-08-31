<?php
/* 
Template Name: Update
*/
// Update all cities
query_posts('posts_per_page=-1');
while (have_posts()) : the_post();
		
	// Get info
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
		$loc_x_[$structure] = get_post_meta($ID, $structure.'-x');
		$loc_y_[$structure] = get_post_meta($ID, $structure.'-y');

		if ( !($loc_x_[$structure][0] == 0 && $loc_y_[$structure][0] == 0) ) {
							
			// Upkeep costs (.02*cost)	
			$cash = get_field('cash','user_'.$user_ID);
			update_field('cash', $cash-(0.02*$cost), 'user_'.$user_ID);

			// Each adds to population (.01*cost)
			$pop = get_field('population',$ID);
			update_field('population', $pop+.01*$cost, $ID);
		}
	}
endwhile;
wp_reset_query();

get_header();
get_footer();
?>