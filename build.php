<?php

// Get ID
$ID = get_the_ID();

// Get structure info
$structure = $_POST['structure'];
$x = $_POST['x'];
$y = $_POST['y'];
$cost = $_POST['structure-cost'];

// Get user info
global $current_user;
get_currentuserinfo();
$cash_current = get_field('cash','user_'.$current_user->ID);

// Make sure we're not bankrupting, then proceed
if (($cash_current - $cost) < 0) {
	echo '<div id="alert">
		  	<p>You can&#39;t do that &mdash; you&#39;d go bankrupt!</p>
		  	<p>Back to <a href="'.bloginfo('home_url').'">main map</a>.</p>
		  </div>';
} else {

	// Take cash from user
	update_field('cash', $cash_current - $cost, 'user_'.$current_user->ID);
	
	// Set structure as built
	// Set location
	$specs = get_field($structure, $ID);
	$specs[0] = array(
		'location-x' => $x,
		'location-y' => $y
	);
	update_field($structure, $specs, $ID);

}

?>