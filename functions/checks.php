<?php

// Alerts
function alerts($current_user) {
	$alerts = array(
		'check_for_update' => $_POST['pass'],
		'check_for_budget_update' => '',
		'check_for_login' => $_GET['login'],
		'check_for_welcome' => '',
		'check_for_profile_update' => '',
		'check_for_scout' => $current_user,
		'check_for_show_hide_scout' => $current_user,
		'check_for_structure' => array('current_user' => $current_user, 'ID' => get_the_ID()),
		'check_for_trade_cancel' => $current_user
		);
	foreach ($alerts as $func => $arg) {
		// If there's a response but it's not just "true" (as in no_bankrupt),
		// display the alert
		if ($func($arg)) {
			return $func($arg); 
		}
	}
}

?>