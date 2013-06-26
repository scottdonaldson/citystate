<?php

// Header checks
function header_checks() {
	check_for_update( $_POST['pass'] );
	check_for_budget_update();
	check_for_login( $_GET['login'] );
	check_for_bankrupt( $_GET['err'] );
	check_for_profile_update();
	check_for_trade_cancel();
	check_for_scout( $current_user );
	check_for_show_hide_scout( $current_user );
}

// City checks
function city_checks() {
	check_for_welcome();
	check_for_structure( $current_user );
}

?>