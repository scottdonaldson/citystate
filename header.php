<!DOCTYPE html>

<!--

	Designed and developed by Scott Donaldson

-->

<?php 

// Define paths
define('MAIN', dirname(__FILE__) . '/');

// Get user info
global $current_user;
get_currentuserinfo(); 

// Run a bunch of checks
header_checks();

// Checks only for cities
if (is_single()) {
	city_checks( $current_user );
}
?>

<html <?php language_attributes(); ?>>
<head>

	<title>City/State</title>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,300,700' rel='stylesheet' type='text/css'>
	
	<link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/images/favicon.ico" />

	<link rel="stylesheet" href="<?php echo bloginfo('template_url'); ?>/style.css" />
	<link rel="stylesheet" href="<?php echo bloginfo('template_url'); ?>/css/city.css" />

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php // Hidden values help us with js! ?>
	<div id="template-url" class="hidden"><?= bloginfo('template_url'); ?>/</div>
	<div id="display-name" class="hidden"><?= $current_user->display_name; ?></div>

	<?php if (isset($alert)) { ?>
		<div id="alert"><?= $alert; ?></div>
	<?php } ?>

	<div id="main" class="clearfix">