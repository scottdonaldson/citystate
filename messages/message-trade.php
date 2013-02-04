<?php
if (isset($_POST['submit'])) {
	if (isset($_POST['trade'])) {
		$trade = $_POST['trade'];
		$from_trade = $_POST['from'];
		$to_trade = $_POST['to'];
		$from_city = $_POST['from_city'];
		$to_city = $_POST['to_city'];

		$from_city_name = get_post($from_city)->post_title;
		$to_city_name = get_post($to_city)->post_title;

		if ($trade == 'approve') {

			update_post_meta(get_the_ID(), 'trade', 'propose-approve');

			$ID = wp_insert_post(array(
				'post_type' => 'message',
				'post_title' => 'Trade route between '.$to_city_name.' and '.$from_city_name.' approved!',
				'post_status' => 'publish'
				)
			);
			add_post_meta($ID, 'to', $to_trade);
			add_post_meta($ID, 'from', $from_trade);
			add_post_meta($ID, 'to-city', $to_city);
			add_post_meta($ID, 'from-city', $from_city);

			add_post_meta($ID, 'read', 'unread'); 

			add_post_meta($ID, 'trade', 'notify-approve');

			// add the route to the meta of each city
			add_post_meta($to_city, 'trade', $from_city);
			add_post_meta($from_city, 'trade', $to_city);

			// update number of trade routes in each city
			$to_routes = get_post_meta($to_city, 'traderoutes', true);
			$from_routes = get_post_meta($from_city, 'traderoutes', true);
			update_post_meta($to_city, 'traderoutes', $to_routes + 1);
			update_post_meta($from_city, 'traderoutes', $from_routes + 1);

			// Now we update the target pop. values of each city.
			// Increases are based on 7.5% of partner's actual population
			$to_pop = floor(0.075 * get_post_meta($to_city, 'population', true)); 
			$from_pop = floor(0.075 * get_post_meta($from_city, 'population', true));
			$to_target_current = get_post_meta($to_city, 'target-pop', true);
			$from_target_current = get_post_meta($from_city, 'target-pop', true);
			update_post_meta($to_city, 'target-pop', $to_target_current + $from_pop);
			update_post_meta($from_city, 'target-pop', $from_target_current + $to_pop);

		} elseif ($trade == 'deny') { 

			update_post_meta(get_the_ID(), 'trade', 'propose-deny');

			$ID = wp_insert_post(array(
				'post_type' => 'message',
				'post_title' => 'Trade route between '.$to_city_name.' and '.$from_city_name.' denied',
				'post_status' => 'publish'
				)
			);
			add_post_meta($ID, 'to', $to_trade);
			add_post_meta($ID, 'from', $from_trade);
			add_post_meta($ID, 'to-city', $to_city);
			add_post_meta($ID, 'from-city', $from_city);

			add_post_meta($ID, 'read', 'unread'); 

			add_post_meta($ID, 'trade', 'notify-deny'); 
		}
	}
} ?>

<section class="trade">

<?php 

// get the cities involved
$to_city = get_post(get_post_meta($ID, 'to-city', true))->post_title;
$from_city = get_post(get_post_meta($ID, 'from-city', true))->post_title; 

switch ($trade) {

	// Open proposal
	case 'propose':

		// If being read by recipient
		// Show form for approving or denying
		if ($user == $to) { ?>
			
			<p>The people of <?php echo $from_user->display_name; ?>'s city of <?php echo $from_city; ?> are interested in forming a trade agreement with your city of <?php echo $to_city; ?>. Take your time, do your research, and when you're ready, you can approve or deny the proposal below:</p>
			<form action="<?php the_permalink(); ?>" method="POST">
				<?php 
				// If the city is full up on trade routes, the user can only deny it! 
				if (get_post_meta(get_post_meta($ID, 'to-city', true), 'traderoutes', true) == get_post_meta(get_post_meta($ID, 'to-city', true), 'ports', true)) { ?>
				<p>Unfortunately, <?php echo $to_city; ?> can't accomodate any more trade routes! Unless you visit the city and cancel one or more, you can only deny this request.</p>
				<?php
				// Otherwise, we're ok to approve
				} else { ?>
				<input type="radio" name="trade" id="approve" value="approve" /><label for="approve">Approve</label><br />
				<?php } ?>
				<input type="radio" name="trade" id="deny" value="deny" /><label for="deny">Deny</label><br />

				<!-- reversing the order of to and from since we're going to be sending a notification to the from user -->
				<input type="hidden" name="to" id="to" value="<?php echo $from_user->ID; ?>" />
				<input type="hidden" name="from" id="from" value="<?php echo $current_user->ID; ?>" />
				<input type="hidden" name="to_city" id="to_city" value="<?php echo get_post_meta($ID, 'from-city', true); ?>" />
				<input type="hidden" name="from_city" id="from_city" value="<?php echo get_post_meta($ID, 'to-city', true); ?>" />
				<input class="button" type="submit" id="submit" name="submit" value="Make it so" />
			</form>
		
		<?php 
		// If being read by sender...
		// Just hang on and be patient
		} elseif ($user == $from) { ?>
			<p>The people of <?php echo $to_user->display_name; ?>'s city of <?php echo $to_city; ?> have not yet responded to your proposal to establish a trade route. Hold your horses.</p>
			
		<?php } 
		break; // break open proposal
		
	// An approved proposal
	case 'propose-approve': 

		// Being read by the user who approved the proposal
		if ($user == $to) { ?>

			<p>The people of <?php echo $from_user->display_name; ?>'s city of <?php echo $from_city; ?> are interested in forming a trade agreement with your city of <?php echo $to_city; ?>.</p>
			<p><strong>You approved this trade route.</strong> Over time, as the cities expand, both will benefit from trade.</p>
			
		<?php
		// Being read by the original sender (but not the official notification)
		} elseif ($user == $from) { ?>

			<p>You sent a proposal to establish a trade route between <?php echo $to_user->display_name; ?>'s city of <?php echo $to_city; ?> and your city of <?php echo $from_city; ?>.</p>
			<p>Good news! This trade route was approved.</p>
			
		<?php }
		break; // break approved proposal

	// A denied proposal
	case 'propose-deny':

		if ($user == $to) { ?>

			<p>The people of <?php echo $from_user->display_name; ?>'s city of <?php echo $from_city; ?> are interested in forming a trade agreement with your city of <?php echo $to_city; ?>.</p>
			<p><strong>You rejected this proposal for a trade route.</strong> We get it. Gotta keep your options open.</p>

		<?php } elseif ($user == $from) { ?>

			<p>You sent a proposal to establish a trade route between <?php echo $to_user->display_name; ?>'s city of <?php echo $to_city; ?> and your city of <?php echo $from_city; ?>.</p>
			<p>Sorry, but this proposal was rejected.</p>
			
		<?php }
		break; // break denied proposal

	// Notification of approval
	case 'notify-approve':

		if ($user == $to) { ?>

			<p>Your proposal to open a trade route between your city of <?php echo $to_city; ?> and <?php echo $from_user->display_name; ?>'s city of <?php echo $from_city; ?> has been approved! Over time, as the cities expand, both will benefit from trade.</p>

		<?php } elseif ($user == $from) { ?>

			<p>You agreed to open a trade route between your city of <?php echo $from_city; ?> and <?php echo $to_user->display_name; ?>'s city of <?php echo $to_city; ?>. Over time, as the cities expand, both will benefit from trade.</p>
				
		<?php }
		break; // break approval notification

	// Notification of deny
	case 'notify-deny':

		if ($user == $to) { ?>

			<p>Your proposal to open a trade route between your city of <?php echo $to_city; ?> and <?php echo $from_user->display_name; ?>'s city of <?php echo $from_city; ?> was rejected. Maybe next time.</p>

		<?php } elseif ($user == $from) { ?>

			<p>You rejected the proposal of a trade route between your city of <?php echo $from_city; ?> and <?php echo $to_user->display_name; ?>'s city of <?php echo $to_city; ?>.</p>

		<?php }
		break; // break deny notification	

} ?>
</section>