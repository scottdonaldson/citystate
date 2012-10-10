<?php
/*
Template Name: To Do
*/
get_header(); ?>

<?php $list = get_field('todo-list'); ?>

<div id="todo">

	<?php foreach ($list as $list) { ?>
		<h2><?php echo $list['version']; ?></h2>
		<ul>
			<?php foreach ($list['version_items'] as $item) { ?>
				<li <?php if ($item['completed'] == 'Yes') { echo 'class="completed"'; } ?>>
					<?php echo $item['description']; ?>
				</li>
			<?php } ?>
		</ul>
	<?php } ?>

</div><!-- #todo -->

<?php get_footer(); ?>