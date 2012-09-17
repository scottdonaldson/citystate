<?php
/* 
Template Name: Update
*/
get_header(); ?>

<div class="container">
	<div class="module">
		<h1 class="header active">Update</h1>
		<div class="content visible">
		<?php 
		// Only admins can run update
		if (is_user_logged_in() && current_user_can('switch_themes')) { ?>
			<p>Run daily update below:</p>
			<form action="" method="post">
				<input type="password" name="pass" />
				<input type="submit" value="Run daily update" />
			</form>
		<?php } else { ?>
			<p>Only logged in administrators can run a daily update. That's just the way it is.</p>
		<?php } ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>