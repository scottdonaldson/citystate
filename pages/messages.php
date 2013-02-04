<?php 
/*
Template Name: Messages
*/
get_header(); the_post();

// Only logged in users can view
if (is_user_logged_in()) { ?>

<div class="container">
	<div class="module">
		<h2 class="header active">Messages - Inbox</h2>
		<div class="content visible clearfix">
			
			<?php 
			// Messages menu
			wp_nav_menu(array('theme_location' => 'messages')); 

			global $current_user;
			get_currentuserinfo(); ?>

			<ul class="messages">
				<li class="message first clearfix">
					<span class="name">From</span>
					<span class="subject">Subject</span>
					<span class="date">Date</span>
				</li>
			<?php 
			// Get all users (to convert logins into display names)
			$users = get_users();

			// Get all messages sent to the current users
			$messages = new WP_query( array(
				'post_type' => 'message',
				'posts_per_page' => -1,
				'meta_key' => 'to',
				'meta_value' => $current_user->ID,
				)
			);
			while ($messages->have_posts()) : $messages->the_post();
				
				$ID = get_the_ID();
				$read = get_post_meta($ID, 'read', true);
				$from = get_post_meta($ID, 'from', true);
				$to = get_post_meta($ID, 'to', true);
				$user = $current_user->ID;

				if ($user == $to) { ?>
					<li class="message <?php echo 'from-'.$from.' '.$read; ?> clearfix">
						<span class="name">
							<?php 
							if ($from == 0) {
								echo '-----';
							} else {
								foreach ($users as $user) { 
									if ( $from == $user->ID) { 
										echo $user->display_name; 
										break; 
									} 
								} 
							} ?>
						</span>
						<span class="subject"><a href="<?php the_permalink(); ?>">
							<?php 
							if (get_the_title()) { 
								the_title();
							} else { 
								echo '[No Subject]';
							} ?></a></span>
						<span class="date">
							<?php 
							// if message was sent today, just show time
							if (date('d-m-Y') == get_the_date('d-m-Y')) {
								echo get_the_date('g:i a');
							// otherwise show month and day
							} else {
								echo get_the_date('M j');
							} ?>
						</span>
					</li>
				<?php }

			endwhile;
			wp_reset_postdata(); ?>
			</ul>
		</div><!-- .content -->
	</div><!-- .module -->
</div><!-- .container -->

<?php } else { ?>

<div class="container">
	<div class="header"></div>
</div>

<?php }
get_footer(); ?>