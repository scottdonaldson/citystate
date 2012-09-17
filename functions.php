<?php

// Load scripts and styles
wp_enqueue_script('jquery');
// wp_enqueue_style(array('farbtastic'));

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

// Register text
add_action('register', 'register_replacement');
function register_replacement( $link ){
	$link = '<a href="' . site_url('wp-login.php?action=register', 'login') . '">' . __('create one') . '</a>';
	return $link;
}

// Registration email send from
add_filter( 'wp_mail_from', 'my_mail_from' );
function my_mail_from( $email ) { return 'admin@scottdonaldson.net'; }
add_filter( 'wp_mail_from_name', 'my_mail_from_name' );
function my_mail_from_name( $name ) { return 'City/State'; }

// No redirect to back-end on bad password
add_action( 'wp_login_failed', 'front_end_login_fail' );
function front_end_login_fail( $username ) {
	$referrer = $_SERVER['HTTP_REFERER'];  
	if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
    	wp_redirect( site_url(). '?login=failed' );
    	exit;
	}
}

// Custom login screen
function my_login_head() {
	$template_url = get_bloginfo('template_url');
	echo "<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
";
	echo "<link rel='stylesheet' href='".$template_url."/css/login-style.css'>";
	echo "<script src='".$template_url."/js/login.js'></script>";
}
add_action('login_head', 'my_login_head');

function loginpage_custom_link() {
	return home_url();
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

// Add commas after thousands in numbers
function th($string) {
	$sep = number_format($string, 0, '', ',');
	return $sep;
}

?>