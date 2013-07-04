<?php

// Load functions
define('MAIN', dirname(__FILE__) . '/');
include ( MAIN . 'functions/alerts.php');
include ( MAIN . 'functions/structures.php');
include( MAIN . 'functions/functions-budget.php');
include( MAIN . 'functions/register.php');
include( MAIN . 'functions/strip-category.php');
include( MAIN . 'functions/functions-footer.php');

// Includes
include ( MAIN . 'includes/geo.php');
include ( MAIN . 'includes/structures.php');
include ( MAIN . 'includes/resources.php');
include ( MAIN . 'includes/terrain.php');

// Region- and city-specific
include ( MAIN . 'functions/functions-single.php');
include ( MAIN . 'functions/functions-region.php');

// All the checks
include ( MAIN . 'functions/checks.php');

// Important built-in: meta() -- shortcut for get_post_meta()
function meta($key) {
    return get_post_meta($post->ID, $key, true);
}

// Make sure user isn't going bankrupt
function no_bankrupt($cash, $cost) {
	if ($cash - $cost < 0) {
		return false;
	} else {
		return true;
	}
}

// Error message for going bankrupt
function bankrupt_message() {
	return '<p>You can&#39;t do that &mdash; you&#39;d go bankrupt!</p>';
}

// Get user's cities
function get_user_cities($current_user) {
	$user_args = array(
		'author' => $current_user->ID,
		'posts_per_page' => -1,
		'orderby' => 'title',
		'order' => 'ASC',
		);
	return new WP_query($user_args);
}

// Happiness values
function get_happiness($happiness) {
	$happy = array();
	if ($happiness < 5) {
		$happy['class'] = 'fleeing';
		$happy['message'] = 'People are fleeing the city in anger!';
	} elseif ($happiness < 10) {
		$happy['class'] = 'extremely_unhappy';
		$happy['message'] = 'Extremely unhappy';
	} elseif ($happiness < 20) {
		$happy['class'] = 'very_unhappy';
		$happy['message'] = 'Very unhappy';
	} elseif ($happiness < 45) {
		$happy['class'] = 'unhappy';
		$happy['message'] = 'Unhappy';
	} elseif ($happiness < 55) {
		$happy['class'] = 'neutral';
		$happy['message'] = 'Neither happy nor unhappy';
	} elseif ($happiness < 80) {
		$happy['class'] = 'happy';
		$happy['message'] = 'Happy';
	} elseif ($happiness < 90) {
		$happy['class'] = 'very_happy';
		$happy['message'] = 'Very happy';
	} elseif ($happiness < 95) {
		$happy['class'] = 'extremely_happy';
		$happy['message'] = 'Extremely happy';
	} else {
		$happy['class'] = 'flocking';
		$happy['message'] = 'People from all over flock to this city!';
	}
	return $happy;
}


// include jQuery
function my_jquery_enqueue() {
    wp_deregister_script('jquery');
    wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js", false, null);
    wp_enqueue_script('jquery');
}

// Register nav menus
register_nav_menus( array(
		'primary' => 'Primary Menu',
		'messages' => 'Messages Menu'
	) 
);

// Admin CSS and JS
function city_admin_css() {
	$template_url = get_bloginfo('template_url');
	echo '<link rel="stylesheet" href="'.$template_url.'/css/admin-style.css" />';
}
add_action('admin_head', 'city_admin_css');

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


/* ------------ CUSTOM POST TYPE: ACTIVITY and MESSAGE ------- */

add_action( 'init', 'create_post_type' );
function create_post_type() {
	register_post_type( 'region',
		array(
			'labels' => array(
				'name' => __( 'Regions' ),
				'singular_name' => __( 'Region' )
			),
			'public' => true,
			'has_archive' => true,
			'menu_position=' => 5,
			'rewrite' => false,
			'with_front' => false,
			'supports' => array(
				'title', 'custom-fields'
			),
		)
	);
	register_post_type( 'activity',
		array(
			'labels' => array(
				'name' => __( 'Activities' ),
				'singular_name' => __( 'Activity' )
			),
			'public' => true,
			'has_archive' => true,
			'menu_position=' => 5,
			'rewrite' => array('slug' => 'activity'),
			'supports' => array(
				'title', 'custom-fields'
			),
		)
	);
	register_post_type( 'message',
		array(
			'labels' => array(
				'name' => __( 'Messages' ),
				'singular_name' => __( 'Message' )
			),
			'public' => true,
			'has_archive' => true,
			'menu_position=' => 5,
			'rewrite' => false,
			'with_front' => false,
			'supports' => array(
				'title', 'editor', 'custom-fields'
			),
		)
	);
}

/* ------------ BEGIN CUSTOM FUNCTIONS ----------- */

// Strip 'city-' from 'city-XXX' where XXX is the ID
function strip_city($string) {
	$id = trim($string, 'city-');
	return $id;
}

// Generate slug from a string
function create_slug($string){
   $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
   return $slug;
}

// Add commas after thousands in numbers
function th($number) {
	return number_format($number);
}

?>