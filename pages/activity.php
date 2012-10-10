<?php 
/*
Template Name: Activity Log
*/
get_header(); ?>

<div class="container">

	<?php 
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$args = array(
		'post_type' => 'activity',
		'posts_per_page' => 10,
		'paged' => $paged,
	);
	$a_query = new WP_Query($args); 

	$i = 0;
	while ($a_query->have_posts()) : 
	$a_query->the_post(); 

		$i++; if ($i <=3) { ?>

		<div class="module">
			<h2 class="header active"><?php the_title(); ?></h2>
			<div class="content visible">
				<?php 
				// New activities get added to the bottom, so need to reverse to show in proper order
				$activities = array_reverse(get_post_custom_values('activity')); 
				foreach ($activities as $key=>$activity) {
					echo '<p>'.$activity.'</p>';
				}
				?>
			</div>
		</div>

		<?php } else { ?>
		<div class="module">
			<h2 class="header"><?php the_title(); ?></h2>
			<div class="content"></div>
		</div>
		<?php } 

	endwhile;
	wp_reset_postdata(); 

	?>

	<a href="<?php the_permalink(); ?>/page/<?php echo $paged + 1; ?>">Older</a>

</div>

<?php get_footer(); ?>