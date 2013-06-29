<?php

function breadcrumbs() {
	echo '<div class="clearfix return">';
	// Show back to world map
	// or back to region map if we're in a city
	if (is_category()) { ?>
		<a href="<?php echo home_url(); ?>">World Map &raquo;</a>
	<?php } elseif (is_single() && get_post_type($post->ID) == 'post') { 
		$region = get_the_category(); ?>
		<a href="<?php echo get_category_link($region[0]->term_id); ?>">Back to <?php echo $region[0]->cat_name; ?> &raquo;</a>
		<a href="<?php echo home_url(); ?>">World Map &raquo;</a>
	<?php } elseif (!is_home()) { ?>
		<a href="<?php echo home_url(); ?>">World Map &raquo;</a>
	<?php }
	echo '</div>';
}

function show_not_logged_in_module() { ?>
	<div class="module">
		<?php 
		wp_login_form(array(
				'label_remember' => __( 'Remember me' ),
				'label_log_in' => __( 'Sign In' ),
				'redirect' => site_url().'?login=success',
			)
		);
		?>
	</div>

	<div class="module">
		<p>You aren't logged in or you don't have an account. Want to <?php wp_register(); ?>?</p>
	</div>
<?php
}

function show_user_module($user) { ?>
	<div class="module">
		<p>
			<strong>
				<a href="<?php echo site_url(); ?>/user/<?php echo $user->user_login; ?>"><?php echo $user->display_name; ?></a>
			</strong>
			<small>
				[<a href="<?php echo wp_logout_url(home_url()); ?>">Log out &raquo;</a>]
			</small>
		</p>
		<p>Cash: <span id="user-cash"><?php echo th(get_user_meta($user->ID, 'cash', true)); ?></span> [<a href="<?php echo site_url(); ?>/budget">View Budget</a>]</p>
				<?php 
				$turns = get_user_meta($user->ID, 'turns', true);
				if ($turns > 1) {
					echo '<p><a href="'.site_url().'/docket">'.$turns.' orders of business</a> on your docket.</p>';
				} elseif ($turns > 0) {
					echo '<p><a href="'.site_url().'/docket">'.$turns.' order of business</a> on your docket.</p>';
				} else {
					echo '<p>No remaining orders of business on your docket.</p>';
				} ?>
		<p>
			<?php 
			$unread = 0;
			$messages = new WP_query(array(
				'post_type' => 'message',
				'posts_per_page' => -1,
				'meta_key' => 'to',
				'meta_value' => $current_user->ID,
				)
			); 
			while ($messages->have_posts()) : $messages->the_post();
				if (get_post_meta(get_the_ID(), 'read', true) == 'unread') {
					$unread++;
				}
			endwhile;
			wp_reset_postdata(); ?>
			<a href="<?php echo home_url(); ?>/messages">
				<?php if ($unread > 0) { echo '<strong>'; } ?>
				Messages <small class="messages">[<?php echo $unread; ?>]</small>
				<?php if ($unread > 0) { echo '</strong>'; } ?>
			</a>
		</p>
	</div>
<?php }

function show_keys_module($user) { ?>
	<div class="module keys">
		<div class="clearfix">
			<div class="key user-city"></div><small>Your Cities</small>
		</div>
		<div class="clearfix">
			<div class="key trade-partner"></div><small>Trade Partners</small>
		</div>
		<form class="clearfix" method="POST">
			<div class="key scout"></div>
			<small>Scouted Territories</small>
			<?php $show = get_user_meta($user->ID, 'show_scouted', true); ?>
			<input class="button" name="show_scouted" id="show_scouted" type="submit" value="<?php if ($show == 'show') { echo 'hide'; } else { echo 'show'; } ?>">
		</form>
	</div>
<?php
}

function show_user_city_module($ID) { ?>
	<div class="module">
		<p>Your City: <strong><?php the_title(); ?></strong></p>
		<p>Population: <?= th(get_post_meta($ID, 'population', true)); ?></p>
		<?php $happy = get_happiness(get_post_meta($ID, 'happiness', true)); ?>
		<small class="face <?php echo $happy['class']; ?>"><?php echo $happy['message']; ?></small><br />
		<p><a href="<?php echo the_permalink($post->ID); ?>/?view=resources">View Resources
			<?php $traderoutes = get_post_meta($ID, 'traderoutes', true);
			if ($traderoutes > 0) { echo ' and Trade ['.$traderoutes.']'; } ?></a></p>		
	</div>
<?php
}

function show_not_user_city_module($ID) { ?>
	<div class="module">
		<p>City: <strong><?php the_title(); ?></strong></p>
		<p>Population: <?= th(get_post_meta($ID, 'population', true)); ?></p>
		<p>Governed by <a href="<?php echo site_url(); ?>/user/<?php echo get_the_author_meta('user_login'); ?>"><?php the_author(); ?></a></p>
	</div>
<?php
}

?>
