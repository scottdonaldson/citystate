<?php
/*
Template Name: Scoreboard
*/
get_header(); ?>

<div id="scoreboard">

	<div class="row header clearfix">
		<div class="nutzername">Name</div>
		<div class="cities"># of Cities</div>
		<div class="cash">Cash</div>
		<div class="total-population">Total Population</div>
	</div>

<?php
$users = get_users('orderby=post_count&order=DESC');
foreach($users as $user) {

	// Only show user if they have at least 1 city
	$cities = count_user_posts($user->ID);
	if ($cities >= 1) { 
		global $current_user;
		get_currentuserinfo(); ?>

		<div class="row clearfix <?php if ($current_user->ID == $user->ID) { echo 'current'; } ?>"><?php
			echo '<div class="nutzername"><a href="'.site_url().'/user/'.$user->user_login.'">'.$user->display_name.'</a></div>';
			echo '<div class="cities">'.$cities.'</div>';
			echo '<div class="cash">'.get_field('cash','user_'.$user->ID).'</div>';
			
			// Get this user's cities
			$posts = get_posts('numberposts=-1&author='.$user->ID);
			foreach ($posts as $post) {
				$totalpop;
				$totalpop = $totalpop + get_field('population');
			}
			echo '<div class="total-population">'.$totalpop.'</div>';
			unset($totalpop);
		echo '</div>';
	}
} ?>

</div><!-- #scoreboard -->

<?php get_footer(); ?>