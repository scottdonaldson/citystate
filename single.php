<?php 
if (isset($_GET['snapshot']) && $_GET['snapshot'] === 'true') {
	include ( MAIN . 'snapshots/city.php');
} else {
// The city map.
function get_city_map() { ?>
	<script>var world = {"tiles":[["water","grass","water","water","hills","hills","hills","grass","water","grass"],["grass","grass","hills","sand","sand","sand","grass","grass","water","water"],["sand","forest","mountains","mountains","water","water","grass","grass","grass","water"],["sand","forest","mountains","mountains","hills","water","grass","sand","sand","water"],["grass","grass","hills","water","hills","sand","grass","sand","sand","sand"],["water","sand","hills","water","water","grass","grass","sand","grass","sand"],["water","sand","grass","water","forest","grass","grass","grass","water","sand"],["sand","grass","forest","forest","sand","forest","grass","water","water","water"],["sand","sand","forest","forest","sand","water","water","water","water","mountains"],["water","sand","sand","grass","sand","water","water","sand","water","hills"]]}</script>
	<script src="<?= bloginfo('template_url'); ?>/js/citymap.js"></script>
<?php } add_action('wp_head', 'get_city_map'); 
function city_constructors() { ?>
	<script src="<?= bloginfo('template_url'); ?>/js/constructors.js"></script>
<?php } add_action('wp_head', 'city_constructors');
get_header(); 
the_post(); 

// Get user info
global $current_user;
get_currentuserinfo();

// Get city ID
$ID = get_the_ID();

?>

<?php 
// Variable for if this is the user's city or not
$is_user_city = is_user_logged_in() && $current_user->ID == get_the_author_meta('ID') ? 'user-city' : 'not-user-city';
?>

<svg id="map" class="<?= $is_user_city; ?>" onload="showCityMap()"></svg>

	<?php
	// Make sure the user is logged in
	// and built this city to be able to modify
	if (is_user_logged_in() && ($current_user->ID == get_the_author_meta('ID'))) { 
		show_city_admin($current_user, $ID, $resources);
	} ?>

<?php 
get_footer(); 
} 
?>