<?php 
// Refresh if this is a confirmed or denied trade proposal
if (isset($_POST['submit'])) {
	header('Location:'.get_permalink());
}

get_header(); the_post(); 

// Get user info
global $current_user;
get_currentuserinfo();
?>

<div class="container">
	<div class="module">
		<h2 class="header active">Message</h2>
		<div class="content visible">
			<?php 
			// Messages menu
			wp_nav_menu( array('theme_location' => 'messages') ); 

			$ID = get_the_ID();
			$from = get_post_meta($ID, 'from', true);
			$to = get_post_meta($ID, 'to', true);
			$user = $current_user->ID;

			// Can only view message if is from or to the user viewing it
			if ($user == $from || $user == $to) { ?>

				<div class="clearfix">
				<span class="alignleft">From: <?php if ($user == $from) { 
						echo 'You'; 
					} else { 
						$users = get_users(); 
						foreach ($users as $from_user) {
							if ($from == $from_user->ID) {
								echo $from_user->display_name;
								break;
							}
						}
					} ?></span>
				<span class="alignleft">|</span>
				<span class="alignleft">To: <?php if ($user == $to) { 
						echo 'You'; 
						update_post_meta($ID, 'read', 'read'); // Once read by the user, set it as read
					} else { 
						$users = get_users(); 
						foreach ($users as $to_user) {
							if ($to == $to_user->ID) {
								echo $to_user->display_name;
								break;
							}
						}
					} ?></span>
				<span class="alignleft">|</span>
				<span class="alignleft"><?php the_date(); ?></span>
				</div>

				<section class="message-content">
					<h2><?php the_title(); ?></h2>
					<?php the_content(); ?>
				</section>

				<?php
				// Is this message a trade proposal?
				// If so, include trade stuff
				$trade = get_post_meta($ID, 'trade', true);
				if (isset($trade)) {
					include( MAIN . '/messages/message-trade.php');
				}

			// Otherwise, obvi, it's not meant to be read by the person trying to view it.
			// And how would they even find it to begin with? But better safe than sorry.
			} else { ?>
				<p>This message is not for you!</p>
			<?php } ?>

		</div><!-- .content -->
	</div><!-- .module -->

</div><!-- .container -->

<?php get_footer(); ?>