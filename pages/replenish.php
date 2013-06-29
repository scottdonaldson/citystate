<?php 
/*
Template Name: Replenish
*/
get_header(); 
the_post(); 
update_user_meta($current_user->ID, 'cash', 10000);
?>

<div class="container">

	<div class="module">
		<h1 class="header">Your cash should now be 10,000!</h1>
	</div>

</div><!-- .container -->

<?php get_footer(); ?>