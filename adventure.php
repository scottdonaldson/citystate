<?php 
/*
Template Name: Adventure
*/

// See if we're returning here from having submitted a form
if (isset($_POST['submit'])) {
	$adv = $_POST['adv'];
// If not, choose a random adventure
} else {
	$adv = rand(1, 3);
}

get_header();

// Get user information
global $current_user;
get_currentuserinfo();

$turns = get_field('turns', 'user_'.$current_user->ID);

// If no turns left, abort
if ($turns == 0) { ?>

<div class="container">
	<div class="module">
		<div class="content visible">
			<p>You've got no turns left!</p>
		</div>
	</div>
</div>

<?php 
// Otherwise, move on
} else {

// Remove one turn from the user
update_field('turns', $turns - 1, 'user_'.$current_user->ID);

include( MAIN . 'adventure/' . $adv . '.php' );

}

get_footer();
?>