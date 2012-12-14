<?php 

/* ------------------- *\
   STADIUM VS. STADIUM
\* ------------------- */

/* In this adventure, we:

   1. Pick a random city from the current user's cities
   2. See if there's a stadium in this city (if not, pick another random city)
   3. Pick a random city from all other cities
   4. See if there's a stadium in that city (if not, pick another random city)
   5. If either the user's cities or everyone else's cities have no stadium, we abort 
      and pick another adventure
   6. If we've got our two stadiums, we pick a winner, random but slightly influenced
      by the respective happiness of the two cities
   7. The winning city receives a slight happiness bump and the losing a slight loss of happiness
   8. Lastly, update the activity log and scoreboard
*/

// Look for a stadium in the user's cities
$user_args = array(
	'author' => $current_user->ID,
	'posts_per_page' => -1,
	'orderby' => 'rand',
	);
$user_query = new WP_query($user_args);
while ($user_query->have_posts()) : $user_query->the_post();

	if (get_post_meta(get_the_ID(), 'stadium-y', true) != 0) {
		$user_ID = get_the_ID();
		$user_city = get_the_title();
		$user_link = get_permalink();
		$user_pop = get_post_meta($user_ID, 'population', true);
		$user_stadium = true;

		// Stop the search
		break;
	}
endwhile;
wp_reset_postdata();

// Look for a stadium in every other city
$foe_args = array(
	'posts_per_page' => -1,
	'post__not_in' => array($user_ID),
	'orderby' => 'rand',
	);
$foe_query = new WP_query($foe_args);
while ($foe_query->have_posts()) : $foe_query->the_post();

	if (get_post_meta(get_the_ID(), 'stadium-y', true) != 0) {
		$foe_ID = get_the_ID();
		$foe_city = get_the_title();
		$foe_link = get_permalink();
		$foe_pop = get_post_meta($foe_ID, 'population', true);
		$foe_stadium = true;

		// Check to see if the foe city is one of the user's own cities (it happens!)
		if (get_the_author_meta('ID') == $current_user->ID) {
			$foe_self = true;
		}

		// Stop the search
		break;
	}
endwhile;
wp_reset_postdata();

// If the user doesn't have a stadium in any of their cities,
// or there are no stadiums in any other cities,
// move on and pick another random adventure
if ( !$user_stadium || !$foe_stadium ) {
	$adv = rand(2, 4);
	include( MAIN . 'docket/' . $adv . '.php' );

// Otherwise, the game is on!
} else {
// Get structures to use stadium values
include( MAIN . 'structures.php');
$stadium = $structures['stadium'];
?>

<div class="container docket-<?php echo $adv; ?>">
	<div class="module">
		<h2 class="header active">Sports</h2>
		<div class="content visible clearfix">
			<img src="<?php echo bloginfo('template_url'); ?>/images/stadium.png" class="alignleft" alt="Stadium" />
			<?php if ($foe_self == true) { ?>
			<p>It's an intrastate game!</p>
			<?php } ?>
			<p>The team from your city of <a href="<?php echo $user_link; ?>" target="_blank"><?php echo $user_city; ?></a> is playing a game against the team from <?php if ($foe_self == true) { echo 'another city of yours, '; } ?><a href="<?php echo $foe_link; ?>" target="_blank"><?php echo $foe_city; ?></a>.</p>
			<?php 
			// Get a random outcome between 1 and 100
			$outcome = rand(1, 100); 

			// Calculate the difference in happiness (must be between 0 and 100)
			$user_happy = get_post_meta($user_ID, 'happiness', true);
			$foe_happy = get_post_meta($foe_ID, 'happiness', true);

			// Get respective funding (per population) for each city
			$user_funding = get_post_meta($user_ID, 'funding-stadium', true) ? get_post_meta($user_ID, 'funding-stadium', true) : 0.02*$stadium[3];
			$user_funding = $user_funding/$user_pop;
			$foe_funding = get_post_meta($foe_ID, 'funding-stadium', true) ? get_post_meta($foe_ID, 'funding-stadium', true) : 0.02*$stadium[3];
			$foe_funding = $foe_funding/$foe_pop;
			$diff = $user_happy - $foe_happy + 1000*($user_funding - $foe_funding);

			// The chance of winning will be determined by the difference... above 50% of 
			// user's city is happier than foe's, and below 50% if user's city is less 
			// happier than foe's
			$chance = 50 + $diff;

			// USER WIN, FOE LOSS
			if ($outcome < $chance) { 
				if ($foe_self == true) { ?>
					<p><strong>The team from <?php echo $user_city; ?> won!</strong></p>
				<?php } else { ?>
					<p><strong>Your team won!</strong></p>
				<?php } ?>
				<p>The citizens of <a href="<?php echo $user_link; ?>" target="_blank"><?php echo $user_city; ?></a> are a little bit happier for the win. Those of <a href="<?php echo $foe_link; ?>" target="_blank"><?php echo $foe_city; ?></a> will surely cry themselves to sleep.</p>
				<?php
				// Increase happiness of user city
				// If happiness is under 80, increase by 2
				if ($user_happy < 80) {
					update_post_meta($user_ID, 'happiness', $user_happy + 2);
				// From 80 to 99, increase by 1
				} elseif ($user_happy < 100) {
					update_post_meta($user_ID, 'happiness', $user_happy + 1);
				}
				// Decrease happiness of foe city
				// If happiness is above 20, decrease by 2
				if ($foe_happy > 20) {
					update_post_meta($foe_ID, 'happiness', $foe_happy - 2);
				// Below 20, decrease by 1
				} elseif ($foe_happy <= 20 && $foe_happy > 0) {
					update_post_meta($user_ID, 'happiness', $foe_happy - 1);
				}

				// Update records
				update_post_meta($user_ID, 'wins', get_post_meta($user_ID, 'wins', true) + 1);
				update_post_meta($foe_ID, 'losses', get_post_meta($foe_ID, 'losses', true) + 1);

			// USER LOSS, FOE WIN
			} else { 
				if ($foe_self == true) { ?>
					<p><strong>The team from <?php echo $user_city; ?> won!</strong></p>
				<?php } else { ?>
					<p><strong>Your team lost...</strong></p>
				<?php } ?>
				<p>Because of this loss, the citizens of <a href="<?php echo $user_link; ?>" target="_blank"><?php echo $user_city; ?></a> are less happy than before. Meanwhile, the citizens of <a href="<?php echo $foe_link; ?>" target="_blank"><?php echo $foe_city; ?></a> have all got grins on their smug faces...</p>
				<?php
				// Decrease happiness of user city
				// If happiness is above 20, decrease by 2
				if ($user_happy > 20) {
					update_post_meta($user_ID, 'happiness', $user_happy - 2);
				// Below 20, decrease by 1
				} elseif ($user_happy <= 20 && $user_happy > 0) {
					update_post_meta($user_ID, 'happiness', $user_happy - 1);
				}
				// Increase happiness of foe city
				// If happiness is under 80, increase by 2
				if ($foe_happy < 80) {
					update_post_meta($foe_ID, 'happiness', $foe_happy + 2);
				// From 80 to 99, increase by 1
				} elseif ($foe_happy < 100) {
					update_post_meta($foe_ID, 'happiness', $foe_happy + 1);
				}	

				// Update records
				update_post_meta($user_ID, 'losses', get_post_meta($user_ID, 'losses', true) + 1);
				update_post_meta($foe_ID, 'wins', get_post_meta($foe_ID, 'wins', true) + 1);
								
			} 

			// Update win/loss ratio
			$new_user_wins = get_post_meta($user_ID, 'wins', true);
			$new_user_total = $new_user_wins + get_post_meta($user_ID, 'losses', true);
			update_post_meta($user_ID, 'ratio', number_format(100*$new_user_wins/$new_user_total, 2, '.', ','));
			$new_foe_wins = get_post_meta($foe_ID, 'wins', true);
			$new_foe_total = $new_foe_wins + get_post_meta($foe_ID, 'losses', true);
			update_post_meta($foe_ID, 'ratio', number_format(100*$new_foe_wins/$new_foe_total, 2, '.', ','));

		include ( MAIN .'docket/next.php'); ?>
			
		</div>
	</div>
</div>

<?php } // end game is on
?>