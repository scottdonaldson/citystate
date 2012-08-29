<?php

// Load scripts
wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-draggable');

// Register nav menu
register_nav_menu('primary','Primary Menu');

// Admin CSS and JS
function city_admin_css() {
	$template_url = get_bloginfo('template_url');
	echo '<link rel="stylesheet" href="'.$template_url.'/css/admin-style.css" />';
}
add_action('admin_head', 'city_admin_css');

// Admin JS
function city_admin_js() {
	$template_url = get_bloginfo('template_url');
	echo '<script src="'.$template_url.'/js/admin.js"></script>';
}
add_action('admin_footer', 'city_admin_js');

// Remove a few admin pages
function remove_admin() {
	remove_menu_page('link-manager.php');
	remove_menu_page('edit-comments.php');
	remove_menu_page('upload.php');
}
add_action('admin_menu', 'remove_admin');

// Hide admin bar for non-admins
if (!current_user_can('manage_options')) {
	add_filter('show_admin_bar', '__return_false');
}

// Change /author/ permalink to /user/
function custom_author_base() {
	global $wp_rewrite;
	$wp_rewrite->author_base = 'user';
}
add_action('init', 'custom_author_base', 0 );

// Custom login screen
function my_login_head() {
	$template_url = get_bloginfo('template_url');
	echo "<link rel='stylesheet' href='".$template_url."/css/login-style.css'>";
	echo "<script src='".$template_url."/js/login.js'></script>";
}
add_action('login_head', 'my_login_head');

function loginpage_custom_link() {
	return 'http://www.scottdonaldson.net/city/';
}
add_filter('login_headerurl','loginpage_custom_link');

function change_title_on_logo() {
	return 'City/State';
}
add_filter('login_headertitle', 'change_title_on_logo');


/* ------------ BEGIN CUSTOM FUNCTIONS ----------- */

// Strip 'city-' from 'city-XXX' where XXX is the ID
function strip_city($string) {
	$id = trim($string, 'city-');
	return $id;
}

// Generate slug from a string
function create_slug($string){
   $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
   return $slug;
}

?>