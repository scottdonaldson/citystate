<?php 

/* --------------------- *\
   TRADING BETWEEN PORTS
\* --------------------- */

/* In this adventure, we:

   0. See if a trade route has been proposed.
      Display message accordingly.

   1. Pick a random city from the current user's cities
   2. See if there's at least one port in this city (if not, pick another random city)
   3. Pick a random city from all other cities
   4. See if there's at least one port in that city (if not, pick another random city)
   5. If either the user's cities or everyone else's cities have no ports, we abort 
      and pick another adventure
   6. If no trade route exists, propose a trade route
   7. If the other city is not one of the user's, send a message to the other for approval and end
   8. If the other city is one of the user's, allow approval of it here
*/

// Get user info
global $current_user;
get_currentuserinfo();

if (isset($_POST['submit'])) { 

$to = $_POST['to'];
$from = $_POST['from'];
$to_city = $_POST['to_city'];
$from_city = $_POST['from_city'];
$to_city_name = get_post($_POST['to_city'])->post_title;
$from_city_name = get_post($_POST['from_city'])->post_title;

$ID = wp_insert_post(array(
		'post_type' => 'message',
		'post_title' => 'Proposed trade route between '.$to_city_name.' and '.$from_city_name,
		'post_content' => '',
		'post_status' => 'publish'
		)
	);
	add_post_meta($ID, 'to', $to);
	add_post_meta($ID, 'from', $from);
	add_post_meta($ID, 'to-city', $to_city);
	add_post_meta($ID, 'from-city', $from_city);

	add_post_meta($ID, 'read', 'unread'); 

	add_post_meta($ID, 'trade', 'propose'); 
?>

<div class="container">
	<div class="module">
		<h1 class="header">Trade</h1>
		<div class="content">
			<p>Your proposal has been sent!</p>
			<p>Now to play the waiting game...</p>
			<?php include ( MAIN .'docket/next.php'); ?>
		</div>
	</div>
</div>

<?php } else {
// Look for a port in the user's cities
$user_args = array(
	'author' => $current_user->ID,
	'posts_per_page' => -1,
	'orderby' => 'rand',
	);
$user_query = new WP_query($user_args);
while ($user_query->have_posts()) : $user_query->the_post();

	if (get_post_meta(get_the_ID(), 'ports', true) != 0) {
		$user_ID = get_the_ID();
		$user_city = get_the_title();
		$user_link = get_permalink();
		$user_pop = get_post_meta($user_ID, 'population', true);
		$user_ports = get_post_meta($user_ID, 'ports', true);
		$user_routes = get_post_meta($user_ID, 'traderoutes', true) ? get_post_meta($user_ID, 'traderoutes', true) : 0;

		// Make sure there is at least one open port (i.e. more
		// ports than active trade routes)
		if ($user_ports > $user_routes) {
			$user_port = true;
			// Stop the search
			break;
		}
	}
endwhile;
wp_reset_postdata();

// Find what other cities have ports
$other_args = array(
	'author' => -$current_user->ID,
	'posts_per_page' => -1,
	'orderby' => 'rand',
	);
$other_query = new WP_Query($other_args);
$others = array();
while ($other_query->have_posts()) : $other_query->the_post();

	// If there's a port, and there's more ports than trade routes,
	// add this city to the list of potential cities
	if (get_post_meta(get_the_ID(), 'ports', true) != 0 && 
		get_post_meta(get_the_ID(), 'ports', true) > get_post_meta(get_the_ID(), 'traderoutes', true)) {

		array_push($others, array(
			'ID' => get_the_ID(),
			'name' => get_the_title(),
			'link' => get_permalink(),
			'pop' => get_post_meta(get_the_ID(), 'population', true),
			'gov' => get_the_author(),
			'gov_ID' => get_the_author_meta('ID')
			)
		);
	}

endwhile;
wp_reset_postdata();

// Make sure user's city doesn't have any trade routes with these already
$current_trades = get_post_meta($user_ID, 'trade');
foreach ($others as $key=>$other) {
	foreach ($current_trades as $current_trade) {
		if ($other['ID'] == $current_trade) {
			unset($others[$key]);
		}
	}
}

// Next, we check the list of open trade proposals.
// If any of the cities on the list of potentials have open proposals
// with our city, we remove them from the list
$proposals = new WP_Query(array(
	'posts_per_page' => -1,
	'post_type' => 'message',
	'meta_key' => 'trade',
	'meta_value' => 'propose'
	)
);
while ($proposals->have_posts()) : $proposals->the_post();
	foreach ($others as $key=>$other) { 
		if (($user_ID == get_post_meta(get_the_ID(), 'from-city', true) ||
			 $user_ID == get_post_meta(get_the_ID(), 'to-city', true)) &&
			($other['ID'] == get_post_meta(get_the_ID(), 'from-city', true) ||
			 $other['ID'] == get_post_meta(get_the_ID(), 'to-city', true))) {

			unset($others[$key]);
		}
	}
endwhile;
wp_reset_postdata();

// Make sure there's no more than 5 cities outputted
$others = array_slice($others, 0, 5);

// If the user doesn't have any ports in any of their cities,
// or there are no other cities available to trade with,
// move on and pick another random adventure
if ( !$user_port || count($others) == 0) {
	$adv = rand(1, 3);
	include( MAIN . 'docket/' . $adv . '.php' );

// Otherwise propose a trade route
} else { ?>

<div class="container docket-<?php echo $adv; ?>">
	<div class="module">
		<h2 class="header">Trade</h2>
		<div class="content clearfix">
			<img src="<?php echo bloginfo('template_url'); ?>/images/port-e.png" class="alignleft" alt="Port" />
			
			<p>Business representatives from your city of <a class="snapshot" href="<?php echo $user_link; ?>"><?php echo $user_city; ?></a> have been seeking to open up a trade route. From market research, they've found several cities that would make mutually beneficial trade partners:</p>
			<ul>
				<?php foreach ($others as $other) { ?>
				<li>
					<a class="snapshot" href="<?php echo $other['link']; ?>" target="_blank"><?php echo $other['name']; ?></a>&nbsp;<small>(Pop: <?php echo th($other['pop']); ?>) - <?php echo $other['gov']; ?></small>
				</li>
				<?php } ?>
			</ul>
			<form action="<?php the_permalink(); ?>" method="POST">
			<label for="trade">Choose a city to propose trade with:</label>
			<select name="trade" id="trade">
				<option value="none" name="none">--- None ---</option>
				<?php foreach ($others as $other) { ?>
					<option data-id="<?php echo $other['gov_ID']; ?>" value="<?php echo $other['ID']; ?>" name="<?php echo $other['ID']; ?>"><?php echo $other['name']; ?></option>
				<?php } ?>
			</select>
			<p class="helper"></p>

			<input type="hidden" name="to" id="to" />
			<input type="hidden" name="from" id="from" value="<?php echo $current_user->ID; ?>" />

			<input type="hidden" name="to_city" id="to_city" />
			<input type="hidden" name="from_city" id="from_city" value="<?php echo $user_ID; ?>" />

			<input type="hidden" name="adv" id="adv" value="4" />
			<input class="button" type="submit" name="submit" id="submit" value="Propose trade route" />
			</form>

			<!-- run some javascript to help with the form -->
			<script>
				jQuery(window).ready(function($) {
					var trades = $('#trade'),
						selected,
						to = $('#to'),
						toCity = $('#to_city'),
						helper = $('.helper').hide(),
						again = $('.again'),
						submit = $('#submit').hide();
						
					trades.change(function(){
						selected = $('option:selected');

						if (selected.val() != 'none') {
							helper.html('If you seek to establish a trade route, a message will be sent to that city&apos;s governor proposing trade. You will be notified once they either approve or deny your proposition.').show();
							again.hide();
							submit.show();

							to.val(selected.data('id'));
							toCity.val(selected.val());
						} else {
							helper.hide();
							again.show();
							submit.hide();
						}
					});
				});
			</script>
			
		<?php include ( MAIN .'docket/next.php'); ?>
			
		</div>
	</div>
</div>

<?php 
} // end we have ports
} // end form has not been submitted
?>