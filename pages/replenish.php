<?php 
/*
Template Name: Replenish
*/
get_header(); 
the_post(); 
update_user_meta($current_user->ID, 'cash', $_GET['cash']);
?>

<div class="container">

	<div class="module">
		<h1 class="header">Your cash should now be what you declared it.</h1>
	</div>

</div><!-- .container -->

<?php get_footer(); ?>