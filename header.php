<?php 

// Define paths
define('MAIN', dirname(__FILE__) . '/');

// Run a bunch of checks
$alert = alerts( $current_user );

?>

<!DOCTYPE html>

<!--

	Designed and developed by Scott Donaldson

-->

<html <?php language_attributes(); ?>>
<head>

	<title>City/State</title>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,300,700' rel='stylesheet' type='text/css'>
	
	<link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/images/favicon.ico" />

	<link rel="stylesheet" href="<?php echo bloginfo('template_url'); ?>/style.css" />
	<link rel="stylesheet" href="<?php echo bloginfo('template_url'); ?>/css/city.css" />

	<script src='https://cdn.firebase.com/v0/firebase.js'></script>
	<script src="<?= bloginfo('template_url'); ?>/js/vendor/snap.js"></script>

<?php 

wp_head(); 
?>
</head>

<?php
// Conditional classes for <body>
$body_class = '';
if (is_home() || is_single()) {
	$body_class .= 'ocean ';
}
?>
<body <?php body_class($body_class); ?>>

	<?php // Hidden values help us with js! ?>
	<div id="template-url" class="hidden"><?= bloginfo('template_url'); ?>/</div>
	<div id="display-name" class="hidden"><?= $current_user->display_name; ?></div>

	<?php if (isset($alert)) { ?>
		<div id="alert"><?= $alert; ?></div>
	<?php } ?>

	<div id="main" class="clearfix">