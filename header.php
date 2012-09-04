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