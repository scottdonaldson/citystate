<!DOCTYPE html>
<?php 
if (isset($_POST['update'])) { 
	if (is_single()) {
		get_template_part('build');
	}
} ?>
<html <?php language_attributes(); ?>>
<head>

	<title>City/State</title>
	<link rel="stylesheet" href="<?php echo bloginfo('template_url'); ?>/style.css" />
	<link rel="stylesheet" href="<?php echo bloginfo('template_url'); ?>/css/city.css" />
	<link rel="stylesheet" href="<?php echo bloginfo('template_url'); ?>/css/zoom-med.css" />
	<?php get_template_part('colors'); ?>

<?php wp_head(); ?>
</head>

<body <?php body_class('zoom-5'); ?>>