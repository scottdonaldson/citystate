<?php get_header(); ?>

<div class="container">

	<div class="module">
		<h1 class="header active"><?php the_title(); ?></h1>
		<div class="content visible">
			<?php the_content(); ?>
		</div>
	</div>

</div><!-- .container -->

<?php get_footer(); ?>