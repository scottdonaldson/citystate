<?php
/*
Template Name: Scoreboard
*/
get_header(); ?>

<div class="container">

	<div id="scoreboard" class="module">
		<h2 class="header active">Players</h2>
		<div class="content visible">
			<div class="row clearfix">
				<div class="nutzername"><strong>Name</strong></div>
				<div class="cities"><strong># of Cities</strong></div>
				<div class="cash"><strong>Cash</strong></div>
				<div class="total-population"><strong>Total Population</strong></div>
			</div>

		<?php		
		$users = get_users(array(
			'orderby' => 'post_count',
			'order' => 'DESC'
			)
		);
		foreach($users as $user) {

			// Only show user if they have at least 1 city
			$cities = count_user_posts($user->ID);
			if ($cities >= 1) { 
				global $current_user;
				get_currentuserinfo(); ?>

				<div class="row clearfix <?php if ($current_user->ID == $user->ID) { echo 'current'; } ?>"><?php
					echo '<div class="nutzername"><a href="'.site_url().'/user/'.$user->user_login.'">'.$user->display_name.'</a></div>';
					echo '<div class="cities">'.$cities.'</div>';
					echo '<div class="cash">'.th(get_field('cash','user_'.$user->ID)).'</div>';
					
					// Get this user's cities
					$posts = get_posts('numberposts=-1&author='.$user->ID);
					foreach ($posts as $post) {
						$totalpop;
						$totalpop = $totalpop + get_field('population');
					}
					echo '<div class="total-population">'.th($totalpop).'</div>';
					unset($totalpop);
				echo '</div>';
			}
		} ?>
		</div><!-- .content -->
	</div><!-- #scoreboard .module-->

	<div class="module">
		<h2 class="header active">Cities</h2>
		<div class="content visible">
			<?php 
			$place = 0;
			query_posts(
				array(
				'posts_per_page' => 5,
				'orderby' => 'meta_value_num',
				'meta_key' => 'population',
				'order' => 'DESC',
				)
			); while (have_posts()) : the_post(); 
				$place++;
				if (get_the_author() == $current_user->ID) { $current = 'class="current"'; }
				echo '<p '.$current.'>'.$place.'. <a href="'.get_permalink().'">'.get_the_title().'</a> (Pop. '.th(get_post_meta(get_the_ID(), 'population', true)).')</p>';

			endwhile; wp_reset_query(); ?>
		</div>
	</div>

</div>

<?php get_footer(); ?>