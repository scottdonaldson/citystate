<?php get_header(); the_post();

// Get the ID
$id = get_the_author_meta('ID'); ?>

<h1><?php the_author(); ?></h1>
<p>Cash: <?php the_field('cash','user_'.$id); ?></p>

<?php get_footer(); ?>