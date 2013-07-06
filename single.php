<?php 
get_header(); 
the_post(); 

// Get user info
global $current_user;
get_currentuserinfo();

// Get city ID
$ID = get_the_ID();

// Set initial resource costs equal to 0
foreach (get_resources() as $resource => $values) {
	$name = substr($resource[0], 0, -6); // remove '_stock'
	$r_cost[$name] = 0;
}

?>

<?php 
// TODO: figure this out better
// The snapshot (used in AJAX calls)
// include( MAIN . 'single/snapshot.php'); 

// Variable for if this is the user's city or not
$is_user_city = is_user_logged_in() && $current_user->ID == get_the_author_meta('ID') ? 'user-city' : 'not-user-city';
?>

<div id="map" class="clearfix <?= $is_user_city; ?>">

	<?php 
	show_city_map($ID);

	// show the city's geographic neighbors
	show_city_neighbors($ID);
	?>
	
</div><!-- #map -->

	<?php
	// Make sure the user is logged in
	// and built this city to be able to modify
	if (is_user_logged_in() && ($current_user->ID == get_the_author_meta('ID'))) { 
		show_city_admin($current_user, $ID, $resources);
	} ?>

<?php
// If user is viewing something in the city
// (Pull this after rendering the map so that we can use its data)
if (isset($_GET['view'])) { include( MAIN . 'single/view.php'); }
?>

<?php get_footer(); ?>