<!DOCTYPE html>
<?php 
if (isset($_POST['update'])) { 
	if (is_single()) {
		include('build.php');
	}
} 
if ($_GET['login'] == 'failed') {
	$alert = '<p>Bad login. Check your username or password and try again.</p>';
}
if ($_GET['profile'] == 'updated') { 
	if (isset($_POST['pass1']) && isset($_POST['pass2']) && !empty($_POST['pass1']) && $_POST['pass1'] == $_POST['pass2']) {
		$user = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
	    $update = $wpdb->query($wpdb->prepare("UPDATE {$wpdb->users} SET `user_pass` = %s WHERE `ID` = %d", array(wp_hash_password($_POST['pass1']), $user_ID)));
	    if (!is_wp_error($update)) {
	        wp_cache_delete($user_ID, 'users');
	        wp_cache_delete($user->user_login, 'userlogins');
	        wp_logout();
            wp_signon(array('user_login' => $user->user_login,
                           'user_password' => $_POST['pass1']));
            ob_start();
	    }
	} 
	if (isset($_POST['color'])) {
		$user = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
		update_field('color', $_POST['color'], 'user_'.$user->ID);
	}
	$alert = '<p>Profile updated. Keep on keepin&apos; on.</p>';
}
?>
<html <?php language_attributes(); ?>>
<head>

	<title>City/State</title>
	<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700,400italic,700italic' rel='stylesheet' type='text/css'>

	<link rel="stylesheet" href="<?php echo bloginfo('template_url'); ?>/style.css" />
	<link rel="stylesheet" href="<?php echo bloginfo('template_url'); ?>/css/city.css" />
	
	<?php include('colors.php'); ?>

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php if ($alert) { ?>
		<div id="alert"><?php echo $alert; ?></div>
	<?php } ?>

	<div id="main" class="clearfix">