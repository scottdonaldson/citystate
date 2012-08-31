<?php 
/*
Template Name: Create Account
*/

/*
if ( is_array( get_site_option( 'illegal_names' )) && isset( $_GET[ 'new' ] ) && in_array( $_GET[ 'new' ], get_site_option( 'illegal_names' ) ) == true ) {
	wp_redirect( network_home_url() );
	die();
}
*/
function create_acct_stylesheet() {
	?>
	<style type="text/css">
		#toolbar {
			display: none;
		}	
	</style>
	<?php
}

add_action( 'wp_head', 'create_acct_stylesheet' );
get_header(); 
/*
function show_user_form($user_name = '', $user_email = '', $errors = '') {
	// User name
	echo '<label for="user_name">' . __('Username:') . '</label>';
	if ( $errmsg = $errors->get_error_message('user_name') ) {
		echo '<p class="error">'.$errmsg.'</p>';
	}
	echo '<input name="user_name" type="text" id="user_name" value="'. esc_attr($user_name) .'" maxlength="60" /><br />';
	_e( '(Must be at least 4 characters, letters and numbers only.)' );
	?>

	<label for="user_email"><?php _e( 'Email&nbsp;Address:' ) ?></label>
	<?php if ( $errmsg = $errors->get_error_message('user_email') ) { ?>
		<p class="error"><?php echo $errmsg ?></p>
	<?php } ?>
	<input name="user_email" type="text" id="user_email" value="<?php  echo esc_attr($user_email) ?>" maxlength="200" /><br /><?php _e('We send your registration email to this address. (Double-check your email address before continuing.)') ?>
	<?php
	if ( $errmsg = $errors->get_error_message('generic') ) {
		echo '<p class="error">' . $errmsg . '</p>';
	}
	do_action( 'signup_extra_fields', $errors );
}

function signup_user($user_name = '', $user_email = '', $errors = '') {
	global $current_site, $active_signup;

	if ( !is_wp_error($errors) )
		$errors = new WP_Error();

	$signup_for = isset( $_POST[ 'signup_for' ] ) ? esc_html( $_POST[ 'signup_for' ] ) : 'blog';

	// allow definition of default variables
	$filtered_results = apply_filters('signup_user_init', array('user_name' => $user_name, 'user_email' => $user_email, 'errors' => $errors ));
	$user_name = $filtered_results['user_name'];
	$user_email = $filtered_results['user_email'];
	$errors = $filtered_results['errors'];

	?>

	<h2><?php printf( __( 'Get an account at City/State' ), $current_site->site_name ) ?></h2>
	<form id="setupform" method="post" action="<?php echo site_url(); ?>/create-account">
		<input type="hidden" name="stage" value="validate-user-signup" />
		<?php do_action( 'signup_hidden_fields' ); ?>
		<?php show_user_form($user_name, $user_email, $errors); ?>

		<p><input id="signupblog" type="hidden" name="signup_for" value="user" /></p>

		<p class="submit"><input type="submit" name="submit" class="submit" value="<?php esc_attr_e('Next') ?>" /></p>
	</form>
	<?php
}

function validate_user_signup() {
	$result = validate_user_form();
	extract($result);

	if ( $errors->get_error_code() ) {
		signup_user($user_name, $user_email, $errors);
		return false;
	}

	if ( 'blog' == $_POST['signup_for'] ) {
		signup_blog($user_name, $user_email);
		return false;
	}

	wpmu_signup_user($user_name, $user_email, apply_filters( 'add_signup_meta', array() ) );

	confirm_user_signup($user_name, $user_email);
	return true;
}

function confirm_user_signup($user_name, $user_email) {
	?>
	<h2><?php printf( __( '%s is your new username' ), $user_name) ?></h2>
	<p><?php _e( 'But, before you can start using your new username, <strong>you must activate it</strong>.' ) ?></p>
	<p><?php printf( __( 'Check your inbox at <strong>%s</strong> and click the link given.' ), $user_email ); ?></p>
	<p><?php _e( 'If you do not activate your username within two days, you will have to sign up again.' ); ?></p>
	<?php
	do_action( 'signup_finished' );
}

// Main
$active_signup = get_site_option( 'registration' );
if ( !$active_signup )
	$active_signup = 'all';

$active_signup = apply_filters( 'wpmu_active_signup', $active_signup ); // return "all", "none", "blog" or "user"

// Make the signup type translatable.
$i18n_signup['all'] = _x('all', 'Multisite active signup type');
$i18n_signup['none'] = _x('none', 'Multisite active signup type');
$i18n_signup['blog'] = _x('blog', 'Multisite active signup type');
$i18n_signup['user'] = _x('user', 'Multisite active signup type');

if ( is_super_admin() )
	echo '<div class="mu_alert">' . sprintf( __( 'Greetings Site Administrator! You are currently allowing &#8220;%s&#8221; registrations. To change or disable registration go to your <a href="%s">Options page</a>.' ), $i18n_signup[$active_signup], esc_url( network_admin_url( 'settings.php' ) ) ) . '</div>';

$newblogname = isset($_GET['new']) ? strtolower(preg_replace('/^-|-$|[^-a-zA-Z0-9]/', '', $_GET['new'])) : null;

$current_user = wp_get_current_user();
if ( $active_signup == 'none' ) {
	_e( 'Registration has been disabled.' );
} elseif ( $active_signup == 'blog' && !is_user_logged_in() ) {
	if ( is_ssl() )
		$proto = 'https://';
	else
		$proto = 'http://';
	$login_url = site_url( 'wp-login.php?redirect_to=' . urlencode($proto . $_SERVER['HTTP_HOST'] . '/wp-signup.php' ));
	echo sprintf( __( 'You must first <a href="%s">log in</a>, and then you can create a new site.' ), $login_url );
} else {
	$stage = isset( $_POST['stage'] ) ?  $_POST['stage'] : 'default';
	switch ( $stage ) {
		case 'validate-user-signup' :
			if ( $active_signup == 'all' || $_POST[ 'signup_for' ] == 'blog' && $active_signup == 'blog' || $_POST[ 'signup_for' ] == 'user' && $active_signup == 'user' )
				validate_user_signup();
			else
				_e( 'User registration has been disabled.' );
		break;
		case 'validate-blog-signup':
			if ( $active_signup == 'all' || $active_signup == 'blog' )
				validate_blog_signup();
			else
				_e( 'Site registration has been disabled.' );
			break;
		case 'gimmeanotherblog':
			validate_another_blog_signup();
			break;
		case 'default':
		default :
			$user_email = isset( $_POST[ 'user_email' ] ) ? $_POST[ 'user_email' ] : '';
			do_action( 'preprocess_signup_form' ); // populate the form from invites, elsewhere?
			if ( is_user_logged_in() && ( $active_signup == 'all' || $active_signup == 'blog' ) )
				signup_another_blog($newblogname);
			elseif ( is_user_logged_in() == false && ( $active_signup == 'all' || $active_signup == 'user' ) )
				signup_user( $newblogname, $user_email );
			elseif ( is_user_logged_in() == false && ( $active_signup == 'blog' ) )
				_e( 'Sorry, new registrations are not allowed at this time.' );
			else
				_e( 'You are logged in already. No need to register again!' );

			if ( $newblogname ) {
				$newblog = get_blogaddress_by_name( $newblogname );

				if ( $active_signup == 'blog' || $active_signup == 'all' )
					printf( __( '<p><em>The site you were looking for, <strong>%s</strong> does not exist, but you can create it now!</em></p>' ), $newblog );
				else
					printf( __( '<p><em>The site you were looking for, <strong>%s</strong>, does not exist.</em></p>' ), $newblog );
			}
			break;
	}
}
do_action( 'after_signup_form' );
*/
?>
<h1>Hey!</h1>
<p>So, this is awkward, but you can't actually register a new account yet. Sorry.</p>
<p>Back to <a href="<?php echo home_url(); ?>">main map</a>.</p>

<?php get_footer(); ?>
