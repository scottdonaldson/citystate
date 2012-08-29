<?php get_header(); 

// Get user info
$user = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));

// Get the ID
$id = $user->ID; ?>

<h1><?php echo $user->nickname; ?></h1>
<p>Cash: <?php the_field('cash','user_'.$id); ?></p>
<p>Cities: <?php echo count_user_posts($id); ?></p>
	<ul>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<li><?php the_title(); ?> (Pop: <?php the_field('population'); ?>)</li>
	<?php endwhile; endif; ?>
	</ul>

<?php get_footer(); ?>