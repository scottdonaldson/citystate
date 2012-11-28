<?php 
/*
Template Name: Adventure
*/

// Get user information
global $current_user;
get_currentuserinfo();
$turns = get_field('turns', 'user_'.$current_user->ID);


// See if we're returning here from having submitted a form
if (isset($_POST['submit'])) {
	$adv = $_POST['adv'];

// If not, choose a random order of business.
} else {
	$adv = rand(1, 3);
}

get_header();

// Make sure it's a logged in user
if (!is_user_logged_in()) { ?>

	<div class="container">
		<div class="module">
			<div class="content visible">
				<p>Orders of business on the docket are only accessible by governors of cities. It wouldn't make sense for some stranger to come in and start messing with official government business, right?</p>
			</div>
		</div>
	</div>

<?php } else {

	// If no turns left, abort
	if ($turns == 0) { ?>

	<div class="container">
		<div class="module">
			<div class="content visible">
				<p>There are no orders of business left to be taken care of today. Go take a break!</p>
			</div>
		</div>
	</div>

	<?php 
	// Otherwise, move on
	} else {
		// Assuming the user is NOT returning from a submitted form...
		if (!isset($_POST['submit'])) {
			// Remove one turn from the user
			update_field('turns', $turns - 1, 'user_'.$current_user->ID);
		}
	include( MAIN . 'adventure/' . $adv . '.php' );
	}

} // end logged in

get_footer();
?>