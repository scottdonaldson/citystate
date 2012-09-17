<?php

// Register text (shows above toolbar if not logged in)
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

// Give 1000 cash to new users
add_action( 'user_register', 'cash_it_up');
function cash_it_up($id) {
    global $wpdb;
    update_field('cash', 1000, 'user_'.$id); 
}

// No redirect to back-end on bad password
add_action( 'wp_login_failed', 'front_end_login_fail' );
function front_end_login_fail( $username ) {
	$referrer = $_SERVER['HTTP_REFERER'];  
	if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
    	wp_redirect( site_url(). '?login=failed' );
    	exit;
	}
}

?>