<?php 
if (isset($_GET['snapshot']) && $_GET['snapshot'] === 'true') {
	include ( MAIN . 'snapshots/region.php');
} else {
// If an admin is editing the region, include some special CSS and JS
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

if (current_user_can('switch_themes') && $_GET['edit'] == 'true') {

	// If we're submitting some edits to the region, register them now
	if (isset($_POST['buildregion'])) {
		build_region($ID);
	}
?>

<form id="map" class="clearfix" method="POST">

	<?php 
	show_region_map($current_user, $ID);
	show_region_neighbors($current_user, $ID);
	?>

	<input class="button" name="buildregion" id="buildregion" type="submit" value="Save Changes">

</form><!-- #map -->

<?php show_region_overlays($resources); ?>

<?php } else { ?>

<div id="map">
	<?php 
	show_region_map($current_user, $ID);
	show_region_neighbors($current_user, $ID);
	?>
</div>

<?php }

if (is_user_logged_in()) {
	show_region_admin($current_user, $ID);
}

get_footer(); 
} ?>