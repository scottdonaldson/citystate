<?php
// Check for the big update:
// Check for the right password, and if it's good, run the update.
function check_for_update($pass) {
	if (isset($pass)) {
		if (md5($pass) == 'd7bd30db1b34953089b9e33e5a2d4b3b') {
			include ( MAIN . 'functions/update.php');
			$alert = '<p>Daily update complete.</p>';
		} else {
			$alert = '<p>Bad password. Best try again.</p>';
		}
	}
}

// Check for budget update
function check_for_budget_update() {
	if (isset($_POST['submit'])) {
		$alert = 'Budget updated. Happy fiscal new year!';
	} 
}

// Validate the user login.
function check_for_login($login) {
	if (isset($login) && $login == 'failed') {
		$alert = '<p>Bad login. Check your username or password and try again.</p><p><a href="'.home_url().'/wp-login.php?action=lostpassword">Did you forget your password?</a></p>';
	}
} 

// Make sure that the user isn't doing something that would make them go bankrupt.
function check_for_bankrupt($err) {
	if (isset($err) && $err == 'bankrupt') {
		$alert = '<p>You can&#39;t do that &mdash; you&#39;d go bankrupt!</p>';
	}
}

// Check for user updating profile
function check_for_profile_update() {
	if (isset($_GET['profile']) && $_GET['profile'] == 'updated') { 
		$user = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));

		if (isset($_POST['displayName']) && $_POST['displayName'] !== '') {
			$name = $_POST['displayName'];
			wp_update_user(array('ID'=>$user->ID, 'display_name'=>$name));
			update_user_meta('user_'.$user->ID, 'name_change', 1);
		}
		if (isset($_POST['pass1']) && isset($_POST['pass2']) && !empty($_POST['pass1']) && $_POST['pass1'] == $_POST['pass2']) {
			$update = $wpdb->query($wpdb->prepare("UPDATE {$wpdb->users} SET `user_pass` = %s WHERE `ID` = %d", array(wp_hash_password($_POST['pass1']), $user_ID)));
		    if (!is_wp_error($update)) {
		        wp_cache_delete($user_ID, 'users');
		        wp_cache_delete($user->user_login, 'userlogins');
		        wp_logout();
	            wp_signon(array('user_login' => $user->user_login,
	                           'user_password' => $_POST['pass1']));
	            ob_start();
		    }
		} 
		header('Location: '.home_url().'/user/'.$user->user_login);
		$alert = '<p>Profile updated. Keep on keepin&apos; on.</p>';
	}
}

// Check for user canceling any trade routes
function check_for_trade_cancel() {
	if (isset($_POST['cancel'])) {
		$traderoutes = $_POST['traderoute'];

		// For each trade route being canceled...
		foreach ($traderoutes as $traderoute) {
			// Dump the route itself
			delete_post_meta($ID, 'trade', $traderoute); // This city with partner
			delete_post_meta($traderoute, 'trade', $ID); // Partner with city
			// Update number of routes in each city
			update_post_meta($ID, 'traderoutes', get_post_meta($ID, 'traderoutes', true) - 1);
			update_post_meta($traderoute, 'traderoutes', get_post_meta($traderoute, 'traderoutes', true) - 1);
		}
		
		// Display confirmation
		if (count($traderoutes) == 1) {
			$trade_update = '<p>1 route successfully canceled.</p>';
		} else {
			$trade_update = '<p>'.count($traderoutes).' routes successfully canceled.</p>';
		}

		// Send a message
		$notify = wp_insert_post(array(
			'post_type' => 'message',
			'post_title' => 'Trade route between '.get_post($traderoute)->post_title.' and '.get_post($ID)->post_title.' has been canceled',
			'post_content' => $current_user->display_name.' canceled the trade route between '.get_post($ID)->post_title.' and your city of '.get_post($traderoute)->post_title.'. This will take some getting used to.',
			'post_status' => 'publish'
			)
		);
		add_post_meta($notify, 'to', get_post($traderoute)->post_author);
		add_post_meta($notify, 'from', $current_user->ID);
		add_post_meta($notify, 'read', 'unread');

		// Now we update the target pop. values of each city (going down).
		// Decreases are based on 7.5% of partner's actual population
		$to_pop = floor(0.075 * get_post_meta($traderoute, 'population', true)); 
		$from_pop = floor(0.075 * get_post_meta($ID, 'population', true));
		$to_target_current = get_post_meta($traderoute, 'target-pop', true);
		$from_target_current = get_post_meta($ID, 'target-pop', true);
		update_post_meta($to_city, 'target-pop', $to_target_current - $from_pop);
		update_post_meta($from_city, 'target-pop', $from_target_current - $to_pop);
	}
}

// Check for user scouting territories
function check_for_scout($current_user) {
	if (isset($_POST['scout'])) {

		// Get user cash
		$cash_current = get_user_meta($current_user->ID, 'cash', true);

		// Costs 350 to scout
		update_user_meta($current_user->ID, 'cash', $cash_current - 350);

		// And now we're scouting
		update_user_meta($current_user->ID, 'scouting', 'yes');
		update_user_meta($current_user->ID, 'scouting_region', $_POST['scout-region']);
		update_user_meta($current_user->ID, 'scouting_x', $_POST['scout-x']);
		update_user_meta($current_user->ID, 'scouting_y', $_POST['scout-y']);

		$alert = '<p>Scouts have been sent to the territory.</p>'.
				 '<p>The scouts will return with a report tomorrow! Be sure to check your messages then.</p>';	
	}
}

// Check for showing/hiding scouted territories
function check_for_show_hide_scout($current_user) {
	if (isset($_POST['show_scouted'])) {
		// Get user info
		$show = $_POST['show_scouted'];

		update_user_meta($current_user->ID, 'show_scouted', $show);
		if ($show == 'show') {
			$alert = '<p>Scouted territories will now be highlighted on the map.';
		} else {
			$alert = '<p>Scouted territories will now be hidden from the map.';
		}
	}

	$alert = '<p>Showing/hiding scouted territories function called successfully</p>';
}

?>