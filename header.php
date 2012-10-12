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
	<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700,400italic,700italic' rel='stylesheet' type='text/css'>

	<link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/images/favicon.ico" />

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