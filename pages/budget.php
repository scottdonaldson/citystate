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
				<form class="budget" method="post" action="<?= get_permalink(); ?>">
					<?php
					$cities = get_user_cities($current_user);

					while ($cities->have_posts()) : $cities->the_post(); ?>

					
					<?php
					$state_income += $income_[$ID];
					$state_expenses += $expenses_[$ID];
					endwhile;
					wp_reset_postdata();
					?>

					<div class="state">
						<h3>State</h3>
						<input type="checkbox" id="budget_warning" name="budget_warning" <?php if (get_user_meta($current_user->ID, 'budget_warning', true) == 1) { echo 'checked'; } ?> /><label class="grey" for="budget_warning"><small>Always warn me if state expenses are greater than state income.</small></label>
						<div class="income">Total Income: <span><?php echo th($state_income); ?></span></div>

						<div class="original hidden" data-original="<?php echo $state_expenses; ?>"></div>
						<div class="expense">Total Expenses: <span><?php echo th($state_expenses); ?></span></div>

						<div class="net"><strong>Total Net: <span><?php echo th($state_income - $state_expenses); ?></span></strong></div>

					</div>
						
					<input method="POST" type="submit" id="submit" name="submit" value="Submit for review" class="button" />
				
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

	while ($user_query->have_posts()) : $user_query->the_post();

		$ID = get_the_ID();
		$link = get_permalink();
		$pop = get_post_meta($ID, 'population', true);

		include ( MAIN . 'structures.php');
		foreach ($structures as $structure) {
			update_post_meta($ID, $structure.'-funding', get_funding_level($ID, $structure));
		}
	endwhile;
	rewind_posts();

	if ($_POST['budget_warning'] == true) {
		update_user_meta($current_user->ID, 'budget_warning', 1);
	} else {
		update_user_meta($current_user->ID, 'budget_warning', 0);
	}

} ?>

<div class="container">
	<div class="module">
		<h2 class="header"><?php echo $current_user->display_name; ?> - Budget Review</h2>
		<div class="content clearfix">
			<?php 
			$state_income = 0;
			$state_expenses = 0;
			?>

			<h3>Cities</h3>
			<form class="budget" method="post" action="<?php echo site_url(); ?>/budget">
				
			</form>	
		</div>
	</div>
</div>

<!-- load JS for the budget -->
<script src="<?= template_url(); ?>/js/budget.js"></script>

<?php 
} // end does user have cities

// if user is not logged in
} else { ?>

<div class="container">
	<div class="header"></div>
</div>

<?php }
get_footer(); ?>