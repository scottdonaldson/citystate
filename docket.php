<?php 
/*
Template Name: Docket
*/

// Get user information
global $current_user;
get_currentuserinfo();
$turns = get_user_meta($current_user->ID, 'turns', true);

// See if we're returning here from having submitted a form
if (isset($_POST['submit'])) {
	$adv = $_POST['adv'];
// If not, choose a random order of business.
} else {
	$adv = rand(1, 5);
}

get_header();

// Make sure it's a logged in user
if (!is_user_logged_in()) { ?>

	<div class="container">
		<div class="module">
			<div class="content">
				<p>Orders of business on the docket are only accessible by governors of cities. It wouldn't make sense for some stranger to come in and start messing with official government business, right?</p>
			</div>
		</div>
	</div>

<?php } else {

	// If no turns left, abort
	if ($turns == 0) { ?>

	<div class="container">
		<div class="module">
			<div class="content">
				<p>There are no orders of business left to be taken care of today. Go take a break!</p>
			</div>
		</div>
	</div>

	<?php 
	// If the user hasn't built any cities yet (thanks Djallal)
	} elseif (count_user_posts($current_user->ID) == 0) { ?>

	<div class="container">
		<div class="module">
			<h1 class="header">Nothin' much to do</h1>
			<div class="content">
				<?php 
				// Remove one turn from the user, just cuz
				update_user_meta($current_user->ID, 'turns', $turns - 1);

				$rand_city = new WP_Query(array(
					'posts_per_page' => 1,
					'orderby' => 'rand'
					)
				);
				while ($rand_city->have_posts()) : $rand_city->the_post();
				$city = get_the_title();
				$link = get_permalink();
				$city_builder = get_the_author();
				endwhile; wp_reset_postdata(); ?>
				
				<p>Until you build at least one city, there isn't really much for you to do around here. You spend some time wandering around <?php echo $city_builder; ?>'s city of <a class="snapshot" href="<?php echo $link; ?>"><?php echo $city; ?></a>, and thinking about how great it would be to have a city of your own. You've got the cash - why not <a href="<?php echo home_url(); ?>">claim some land</a>?</p>
			</div>
		</div>
	</div>

	<?php
	// Otherwise, move on
	} else {
		// Assuming the user is NOT returning from a submitted form...
		if (!isset($_POST['submit'])) {
			// Remove one turn from the user
			update_user_meta($current_user->ID, 'turns', $turns - 1);
		}
		
	include( MAIN . 'docket/' . $adv . '.php' );
	}

} // end logged in

get_footer();
?>