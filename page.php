<?php get_header(); the_post(); ?>

<div class="container">

	<div class="module">
		<h1 class="header"><?php the_title(); ?></h1>
		<div class="content">
			<?php the_content(); ?>
		</div>
	</div>

</div><!-- .container -->

<?php get_footer(); ?>