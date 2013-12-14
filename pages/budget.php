<?php 
/* 
Template Name: Budget
*/
get_header(); 
the_post();

// Only logged in users can view
if (is_user_logged_in()) {

// Users with no cities got no budgets, obvi
if (count_user_posts($current_user->ID) == 0) { 
	$header = 'No cities, no budget';
	$cities = false;
} else {
	$header = $current_user->display_name.' - Budget Review';
	$cities = true;
} ?>

<div class="container">
	<div class="module">
		<h1 class="header"><?= $header; ?></h1>
		<div class="content">
			<?php
			if (!$cities) { ?>
				<p>If you don't even have a single city, how do you expect to be making or losing any money? Perhaps you should go and <a href="<?php echo home_url(); ?>">build one</a>.</p>
			<?php } else { ?>
	
				<h3>Cities</h3>
				<form class="budget" method="POST">
					<?php
					$cities = get_user_cities($current_user);

					while ($cities->have_posts()) : $cities->the_post();

						show_budget_module($current_user, get_the_ID());

					$state_income += $income_[$ID];
					$state_expenses += $expenses_[$ID];
					endwhile;
					wp_reset_postdata();
					
					show_state_module(); ?>
						
					<input type="submit" id="submit" name="submit" value="Submit for review" class="button">
				
				</form>
				<?php
			} ?>	
		</div>
	</div> 
</div>

<?php 
/* If the user has submitted the form to adjust the budget,
   change corresponding values in their cities accordingly. */

if (isset($_POST['submit'])) { 

	while ($cities->have_posts()) : $cities->the_post();

		$ID = get_the_ID();
		$link = get_permalink();
		$pop = get_post_meta($ID, 'population', true);

		foreach ($structures as $structure) {
			update_post_meta($ID, $structure.'-funding', get_funding($ID, $structure));
		}
	endwhile;
	rewind_posts();

	if ($_POST['budget_warning'] == true) {
		update_user_meta($current_user->ID, 'budget_warning', 1);
	} else {
		update_user_meta($current_user->ID, 'budget_warning', 0);
	}

} ?>

<!-- load JS for the budget -->
<script src="<?= bloginfo('template_url'); ?>/js/budget.js"></script>

<?php 

// if user is not logged in
} else { ?>

<div class="container">
	<div class="header"></div>
</div>

<?php }
get_footer(); ?>