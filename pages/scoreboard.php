<?php
/*
Template Name: Scoreboard
*/
get_header(); ?>

<div class="container">

	<div class="module">
		<div class="content visible">
			<p>The people of Originalia and Secondo keep meticulous records. Current events and cumulative tallies may be found here. Those of record may be found in the <a href="<?php echo home_url(); ?>/log">Log</a>.</p>
		</div>
	</div>
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
					echo '<div class="nutzername"><a href="'.home_url().'/user/'.$user->user_login.'">'.$user->display_name.'</a></div>';
					echo '<div class="cities">'.$cities.'</div>';
					echo '<div class="cash">'.th(get_field('cash','user_'.$user->ID)).'</div>';
					
					// Get this user's cities
					$posts = get_posts('numberposts=-1&author='.$user->ID);
					foreach ($posts as $post) {
						$totalpop;
						$totalpop = $totalpop + get_post_meta(get_the_ID(), 'population', true);
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
		<div class="content visible clearfix">
			<div class="board">
				<h3>Most populous cities</h3>
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
					echo '<p>'.$place.'. <a href="'.get_permalink().'"><strong>'.get_the_title().'</strong></a> (Pop. '.th(get_post_meta(get_the_ID(), 'population', true)).') - <a href="'.home_url().'/user/'.get_the_author_meta('user_login').'">'.get_the_author_meta('display_name').'</a></p>';

				endwhile; wp_reset_query(); ?>
			</div>
			<div class="board">
				<h3>Happiest cities</h3>
				<?php 
				$place = 0;
				query_posts(
					array(
					'posts_per_page' => 5,
					'orderby' => 'meta_value_num',
					'meta_key' => 'happiness',
					'order' => 'DESC',
					)
				); while (have_posts()) : the_post(); 
					$place++;
					echo '<p>'.$place.'. <a href="'.get_permalink().'"><strong>'.get_the_title().'</strong></a> - <a href="'.home_url().'/user/'.get_the_author_meta('user_login').'">'.get_the_author_meta('display_name').'</a></p>';

				endwhile; wp_reset_query(); ?>
			</div>
			<div class="board">
				<h3>Most educated cities</h3>
				<?php 
				$place = 0;
				query_posts(
					array(
					'posts_per_page' => 5,
					'orderby' => 'meta_value_num',
					'meta_key' => 'education',
					'order' => 'DESC',
					)
				); while (have_posts()) : the_post(); 
					$place++;
					echo '<p>'.$place.'. <a href="'.get_permalink().'"><strong>'.get_the_title().'</strong></a> - <a href="'.home_url().'/user/'.get_the_author_meta('user_login').'">'.get_the_author_meta('display_name').'</a></p>';

				endwhile; wp_reset_query(); ?>
			</div>
			<div class="board">
				<h3>Cities with best culture</h3>
				<?php 
				$place = 0;
				query_posts(
					array(
					'posts_per_page' => 5,
					'orderby' => 'meta_value_num',
					'meta_key' => 'culture',
					'order' => 'DESC',
					)
				); while (have_posts()) : the_post(); 
					$place++;
					echo '<p>'.$place.'. <a href="'.get_permalink().'"><strong>'.get_the_title().'</strong></a> - <a href="'.home_url().'/user/'.get_the_author_meta('user_login').'">'.get_the_author_meta('display_name').'</a></p>';

				endwhile; wp_reset_query(); ?>
			</div>
			<div class="board">
				<h3>Most sports wins</h3>
				<?php
				$place = 0;
				query_posts(
					array(
					'posts_per_page' => 5,
					'orderby' => 'meta_value_num',
					'meta_key' => 'wins',
					'order' => 'DESC',
					)
				); while (have_posts()) : the_post(); 
					$place++;
					$wins = get_post_meta(get_the_ID(), 'wins', true);
					$losses = get_post_meta(get_the_ID(), 'losses', true);
					echo '<p>'.$place.'. <a href="'.get_permalink().'"><strong>'.get_the_title().'</strong></a> ('.$wins.'-'.$losses.') - <a href="'.home_url().'/user/'.get_the_author_meta('user_login').'">'.get_the_author_meta('display_name').'</a></p>';

				endwhile; wp_reset_query(); ?>
			</div>
			<div class="board">
				<h3>Best win/loss ratio</h3>
				<?php
				$place = 0;
				$ratio_args = array(
					'posts_per_page' => 5,
					'orderby' => 'meta_value_num',
					'meta_key' => 'ratio',
					'order' => 'DESC',
					); 
				$ratio_query = new WP_query($ratio_args);
				while ($ratio_query->have_posts()) : $ratio_query->the_post(); 
					$place++;
					$ratio = get_post_meta(get_the_ID(), 'ratio', true);
					echo '<p>'.$place.'. <a href="'.get_permalink().'"><strong>'.get_the_title().'</strong></a> ('.$ratio.'%) - <a href="'.home_url().'/user/'.get_the_author_meta('user_login').'">'.get_the_author_meta('display_name').'</a></p>';

				endwhile; wp_reset_postdata(); ?>
			</div>
		</div>
	</div>

</div>

<?php get_footer(); ?>