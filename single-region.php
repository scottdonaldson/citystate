<?php 

if (current_user_can('switch_themes') && $_GET['edit'] == 'true') {

	function region_admin_css() { ?>
		<link rel="stylesheet" href="<?= bloginfo('template_url'); ?>/css/region-admin.css">
	<?php }
	function region_admin_js() { ?>
		<script src="<?= bloginfo('template_url'); ?>/js/region-admin.js"></script>
	<?php }
	add_action('wp_head', 'region_admin_css');
	add_action('wp_footer', 'region_admin_js');
}

get_header(); 
the_post(); 

// Get user info
global $current_user;
get_currentuserinfo();

// Get region ID
$ID = get_the_ID();

// This is an admin-only page
if (current_user_can('switch_themes') && $_GET['edit'] == 'true') {

	if (isset($_POST['buildregion'])) {
		build_region($ID);
	}
?>

<form id="map" class="clearfix" method="POST">

	<?php 
	show_region_map($ID, $resources, $terrain);

	// show the region's geographic neighbors
	show_region_neighbors($geo, $ID, $resources, $terrain);
	?>

	<input class="button" name="buildregion" id="buildregion" type="submit" value="Save Changes">

</form><!-- #map -->

<?php show_region_overlays($resources); ?>

<?php } else { ?>

<div id="map">
	<?php 
	show_region_map($ID, $resources, $terrain);

	// show the region's geographic neighbors
	show_region_neighbors($geo, $ID, $resources, $terrain);
	?>
</div>

<?php }

if (is_user_logged_in()) {
	show_region_admin($current_user, $ID);
}

get_footer(); ?>