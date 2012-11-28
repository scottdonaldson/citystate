<?php 

/* ---------------------------- *\
   CITY COUNCIL RECOMMENDATIONS
\* ---------------------------- */

/* In this adventure, we:

   1. Pick a random city from the current user's cities
   2. Get structures. See if there are any that have not been built but that
      have passed 1/2 the point at which they are desired. If there are, suggest building one of them 
      and give funds (between 5 (not %) and 10% of cost of construction).
   3. If there are no desired structures, get the city's happiness. Just a message if above 55 or between 45 and 54.
   4. If happiness is under 45, deliver a warning that the citizens are unhappy. If user has
      already been warned twice in this turn (i.e. third strike), 10% decrease in happiness.
*/

// Pick a random city
$user_args = array(
	'author' => $current_user->ID,
	'posts_per_page' => 1,
	'orderby' => 'rand',
	);
$user_query = new WP_query($user_args);
while ($user_query->have_posts()) : $user_query->the_post();

	$ID = get_the_ID();
	$city = get_the_title();
	$link = get_permalink();
	$pop = get_post_meta($ID, 'population', true);
	include ( MAIN . 'structures.php');

	$needs = array();
	foreach($structures as $structure=>$values) {
		include( MAIN .'structures/values.php');

		// Only run for nonrepeating structures
		if ($max == 1) {
			if (get_post_meta($ID, $structure.'-x', true) == 0 && 
				get_post_meta($ID, $structure.'-y', true) == 0 && 
				$pop >= 0.5*$desired) {
				
				// If there's a structure that's desired, add it to the $needs array
				array_push($needs, $structure);
			}
		} 
	}
endwhile;
wp_reset_postdata();

?>
<div class="container">
	<div class="module">
		<h2 class="header active">City Council</h2>
		<div class="content visible clearfix">
			<img src="<?php echo bloginfo('template_url'); ?>/images/neighborhood.png" class="alignleft" alt="Neighborhood" />
			<p>The city council in <a href="<?php echo $link; ?>"><?php echo $city; ?></a> is meeting to discuss local matters.</p>

			<?php 
			// If there are NOT any structures that should be built...
			if (count($needs) == 0) { 
				$happy = get_post_meta($ID, 'happiness', true);
				
				// If it's a happy city
				if ($happy >= 55) { ?>
					<p>The citizens are quite pleased with your governance. The city council has no specific recommendations at this time other than to keep on keepin' on.</p>
				<?php 
				// Otherwise, it's content
				} elseif ($happy >=45) { ?>
					<p>The citizens are going about their business without too much concern for your performance as a governor. No news is often good news, but if you want to make your citizens happier, you might consider building some parks or new neighborhoods.</p>
				<?php	
				// Otherwise, it's unhappy
				} else { 
					// Update warning
					$warning = get_post_meta($ID, 'warning', true);
					update_post_meta($ID, 'warning', $warning + 1);

					// If less than three strikes, just warn
					if ($warning < 2) {
					?>
						<p>The citizens are ill at ease, and no one's quite sure why. Building new parks and neighborhoods might be a way to restore their satisfaction.</p>
						
						<?php // Call out two strikes...
						if ($warning == 1) { ?>
							<p>This is the second council meeting in a short amount of time where the citizens' discontentment has been brought to your attention. If it happens again soon, your abilities as a leader might be called into question...</p>
						<?php }
					// At third strike (or beyond), reduce happiness by 10%
					} else { ?>
						<p>The citizens are still unhappy, and now they're tired of feeling like they're not being listened to. A large group walks out of the council meeting, shouting curses at you. Their anger spreads to other areas of the city.</p>
						<?php 
						update_post_meta($ID, 'happiness', .9*$happy);
					}
				}
			// If there ARE structures that should be built...
			} else { ?>
				<p>They say that you should build a <strong><?php echo $needs[0]; ?></strong>!</p>
				<?php 
				$pop = get_post_meta($ID, 'population', true);
				// Use the first structure that needs to be built.
				// Funds raised is a random value between 5 (not %) and 
				// 10% of the construction cost
				$fund = rand(5, .1*$structures[$needs[0]][3]); ?>
				<p>A group of concerned citizens have donated <?php echo $fund; ?> in cash to a fund for the construction.</p>
				<?php
				// Update cash
				$cash = get_field('cash', 'user_'.$current_user->ID);
				update_field('cash', $cash + $fund, 'user_'.$current_user->ID);
			} ?>

		<a class="again" href="<?php the_permalink(); ?>">Next order of business</a>
			
		</div>
	</div>
</div>