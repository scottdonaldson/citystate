<?php get_header(); the_post(); ?>

<div class="container">

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

</div>

<?php get_footer(); ?>