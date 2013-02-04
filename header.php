<!DOCTYPE html>

<!--

	Designed and developed by Scott Donaldson

-->

<?php 

// Define paths
define('MAIN', dirname(__FILE__) . '/');

// Check to see if any forms have been submitted
include('header-checks.php'); ?>

<html <?php language_attributes(); ?>>
<head>

	<title>City/State</title>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,300,700' rel='stylesheet' type='text/css'>
	
	<link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/images/favicon.ico" />

	<link rel="stylesheet" href="<?php echo bloginfo('template_url'); ?>/style.css" />
	<link rel="stylesheet" href="<?php echo bloginfo('template_url'); ?>/css/city.css" />
	
	<script src="<?php echo bloginfo('template_url'); ?>/js/jquery.min.js"></script>

	<?php include('colors.php'); ?>

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php // Hidden values help us with js! ?>
	<div id="template-url" class="hidden"><?php echo bloginfo('template_url'); ?>/</div>
	<div id="display-name" class="hidden">
		<?php global $current_user;
      	get_currentuserinfo(); 
      	echo $current_user->display_name; ?>
    </div>

	<?php if (isset($alert)) { ?>
		<div id="alert"><?php echo $alert; ?></div>
	<?php } ?>

	<div id="main" class="clearfix">