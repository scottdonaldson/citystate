<?php 
/* 
Template Name: Budget
*/
get_header(); 

// Only logged in users can view
if (is_user_logged_in()) {

// Users with no cities got no budgets, obvi
if (count_user_posts($current_user->ID) == 0) { ?>

<div class="container">
	<div class="module">
		<h1 class="header">No cities, no budget</h1>
		<div class="content">
			<p>If you don't even have a single city, how do you expect to be making or losing any money? Perhaps you should go and <a href="<?php echo home_url(); ?>">build one</a>.</p>
		</div>
	</div>
</div>

<?php 
// First things first, get user's cities
} else { 
$user_args = array(
	'author' => $current_user->ID,
	'posts_per_page' => -1,
	'orderby' => 'title',
	'order' => 'ASC',
	);
$user_query = new WP_query($user_args);

/* If the user has submitted the form to adjust the budget,
   change corresponding values in their cities accordingly. */

if (isset($_POST['submit'])) { 

	while ($user_query->have_posts()) : $user_query->the_post();

		$ID = get_the_ID();
		$link = get_permalink();
		$pop = get_post_meta($ID, 'population', true);

		include ( MAIN . 'structures.php');
		foreach ($structures as $structure=>$values) {
			include ( MAIN . 'structures/values.php');

			if (isset($_POST[basename($link).'-'.$structure]) && 
				$_POST[basename($link).'-'.$structure] != '') {
				$newfund = $_POST[basename($link).'-'.$structure];
				update_post_meta($ID, 'funding-'.$structure, $newfund);

				$multiples = array('park', 'farm', 'fishery', 'lumberyard', 'port');

				if ($max == 1) {
					if ($pop >= $desired) {
						$need_funding_[$structure] = round(0.02*$cost * (1 + 0.1 * (($pop - $desired) / 1000) ));
					} else {
						$need_funding_[$structure] = 0.02*$cost;
					}
					$diff_[$structure] = $newfund / $need_funding_[$structure];
					if ($diff_[$structure] < 1) {
						$funding = 'bad';
					} elseif ($diff_[$structure] < 1.5) {
						$funding = 'fair';
					} elseif ($diff_[$structure] < 5) {
						$funding = 'good';
					} elseif ($diff_[$structure] >= 5) {
						$funding = 'excellent';
					}

				} elseif (in_array($structure, $multiples)) { 
					$count = get_post_meta($ID, $structure.'s', true);

					$need_funding_[$structure] = $count * round(0.02*$cost * (1 + 0.1 * ((get_post_meta($ID, 'population', true) - $desired) / 1000) ));
					$diff_[$structure] = $newfund / $need_funding_[$structure];
					if ($diff_[$structure] < 1) {
						$funding = 'bad';
					} elseif ($diff_[$structure] < 1.5) {
						$funding = 'fair';
					} elseif ($diff_[$structure] < 5) {
						$funding = 'good';
					} elseif ($diff_[$structure] >= 5) {
						$funding = 'excellent';
					}
				}
				update_post_meta($ID, $structure.'-funding', $funding);
			}
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
			if (isset($_POST['submit'])) { ?>
				<small class="grey">Budget updated. Happy fiscal new year!</small>
			<?php } ?>

			<h3>Cities</h3>
			<form class="budget" method="post" action="<?php echo site_url(); ?>/budget">
				<?php
				while ($user_query->have_posts()) : $user_query->the_post(); 
					$ID = get_the_ID();
					$city = get_the_title();
					$link = get_permalink();
					$pop = get_post_meta($ID, 'population', true); 
					$happiness = get_post_meta($ID, 'happiness', true); ?>

				<div id="city-<?php the_ID(); ?>" class="board">
					<h3><a class="snapshot" href="<?php echo $link; ?>"><?php echo $city; ?></a> <small>(Pop: <?php echo th(get_post_meta($ID, 'population', true)); ?>)</small></h3>
					<?php if ($happiness < 5) {
							$happy = 'fleeing';
							$message = 'People are fleeing the city in anger!';
						} elseif ($happiness < 10) {
							$happy = 'extremely_unhappy';
							$message = 'Extremely unhappy';
						} elseif ($happiness < 20) {
							$happy = 'very_unhappy';
							$message = 'Very unhappy';
						} elseif ($happiness < 45) {
							$happy = 'unhappy';
							$message = 'Unhappy';
						} elseif ($happiness < 55) {
							$happy = 'neutral';
							$message = 'Neither happy nor unhappy';
						} elseif ($happiness < 80) {
							$happy = 'happy';
							$message = 'Happy';
						} elseif ($happiness < 90) {
							$happy = 'very_happy';
							$message = 'Very happy';
						} elseif ($happiness < 95) {
							$happy = 'extremely_happy';
							$message = 'Extremely happy';
						} else {
							$happy = 'flocking';
							$message = 'People from all over flock to this city!';
						} ?>
					<small class="face <?php echo $happy; ?>"><?php echo $message; ?></small>
					<?php include ( MAIN . 'structures.php'); ?>
					<div class="structures clearfix">
					<?php foreach ($structures as $structure=>$values) {
						include ( MAIN . 'structures/values.php');

						// Determine placeholder value. If funding is set, then that funding,
						// otherwise the minimum of .02 times the cost to build
						if (get_post_meta($ID, 'funding-'.$structure, true)) {
							$placeholder_[$structure] = get_post_meta($ID, 'funding-'.$structure, true);
						} else {
							// Nonrepeating
							if ($max == 1) {
								$placeholder_[$structure] = 0.02 * $cost; 
							// Repeating
							} else {
								$count = get_post_meta($ID, $structure.'s', true);
								$placeholder_[$structure] = 0.02 * $cost * $count; 
							}
						}

						// Output labels and inputs for nonrepeating and some repeating structures
						if ($max == 1) {
							if (get_post_meta($ID, $structure.'-y', true) != 0) { 
								// If so, then there is at least one structure
								$at_least_one_[$ID] = true; 

								// Determine if placeholder is bad, fair, good, or excellent based on value needed.
								// (see header-checks.php for details on $need_funding formula)
								if ($pop >= $desired) {
									$need_funding_[$structure] = round(0.02*$cost * (1 + 0.1 * (($pop - $desired) / 1000) ));
								} else {
									$need_funding_[$structure] = 0.02*$cost;
								}
								$diff_[$structure] = $placeholder_[$structure] / $need_funding_[$structure];
								if ($diff_[$structure] < 1) {
									$funding = 'bad';
								} elseif ($diff_[$structure] < 1.5) {
									$funding = 'fair';
								} elseif ($diff_[$structure] < 5) {
									$funding = 'good';
								} elseif ($diff_[$structure] >= 5) {
									$funding = 'excellent';
								} ?>
								<div class="clearfix">
									<label for="<?php echo basename($link).'-'.$structure; ?>"><?php echo ucfirst($name); ?><small> (Min: <?php echo .02*$cost; ?>)</small></label>
									<input type="number" id="<?php echo basename($link).'-'.$structure; ?>" name="<?php echo basename($link).'-'.$structure; ?>" max="<?php echo 10*$need_funding_[$structure]; ?>" min="<?php echo .02*$cost; ?>" placeholder="<?php echo th($placeholder_[$structure]); ?>" class="funding-<?php echo $funding; ?>" />
								</div><!-- just a clearfix -->
							<?php }

						// Some structures require funding but can build multiple
						$multiples = array('park', 'farm', 'fishery', 'lumberyard', 'port');
						} elseif (in_array($structure, $multiples)) { 
							$count = get_post_meta($ID, $structure.'s', true);
							if ($count > 0) {
								$at_least_one_[$ID] = true; 

								// Determine if placeholder is bad, fair, good, or excellent based on value needed.
								// (see header-checks.php for details on $need_funding formula)
								$need_funding_[$structure] = $count * round(0.02*$cost * (1 + 0.1 * ((get_post_meta($ID, 'population', true) - $desired) / 1000) ));
								$diff_[$structure] = $placeholder_[$structure] / $need_funding_[$structure];
								if ($diff_[$structure] < 1) {
									$funding = 'bad';
								} elseif ($diff_[$structure] < 1.5) {
									$funding = 'fair';
								} elseif ($diff_[$structure] < 5) {
									$funding = 'good';
								} elseif ($diff_[$structure] >= 5) {
									$funding = 'excellent';
								} ?>
								<div class="clearfix">
									<label for="<?php echo basename($link).'-'.$structure; ?>">
										<?php if ($count == 1) { echo ucfirst($name); } else { echo $count.' '.ucfirst($plural); } ?><small> (Min: <?php echo .02*$cost*$count; ?>)</small></label>
									<input type="number" id="<?php echo basename($link).'-'.$structure; ?>" name="<?php echo basename($link).'-'.$structure; ?>" max="<?php echo 10*$need_funding_[$structure]; ?>" min="<?php echo 0.02*$cost*$count; ?>" placeholder="<?php echo th($placeholder_[$structure]); ?>" class="funding-<?php echo $funding; ?>" />
								</div><!-- just a clearfix -->
							<?php }
						}
					} 

					// If there's no structures that require funding...
					if (!isset($at_least_one_[$ID])) {
						echo '<p>No structures in this city require funding.</p>';
					} ?>
					</div><!-- .structures -->
					
					<section>
						<p class="alignleft">City Income:</p>
						<p class="alignright income"><?php $income_[$ID] = ceil(0.05*$pop); echo th($income_[$ID]); ?></p>

						<p class="alignleft">City Expenses: <?php
							$expenses_[$ID] = 0;
							foreach ($structures as $structure=>$values) {
								include ( MAIN . 'structures/values.php');
								
								if ($max == 1) {
									if (get_post_meta($ID, $structure.'-y', true) != 0) { 
										$expenses_[$ID] += $placeholder_[$structure];
									}
								} elseif (in_array($structure, $multiples)) {
									if (get_post_meta($ID, $structure.'s', true) > 0) {
										$expenses_[$ID] += $placeholder_[$structure];
									}
								}
							}
						?></p>
						<p class="alignright expense"><?php echo th($expenses_[$ID]); ?></p>

						<strong class="alignleft">City Net:</strong>
						<strong class="alignright net"><?php echo th($income_[$ID] - $expenses_[$ID]); ?></strong>
					</section>

					<div class="original hidden" data-original="<?php echo $expenses_[$ID]; ?>"></div>
				</div>
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
		</div>
	</div>
</div>

<script>
	jQuery(document).ready(function($){
		var input = $('input[type="number"]'),
			origStateExpense = parseInt($('.state .original').data('original')),
			stateExpense = $('.state .expense span');

		(function($){
			$.fn.extend({
		        updateTotal: function() {

		        	$('.warning').remove();
		        	
		        	$this = $(this);
		        	
		        	var cityIncome = parseInt(noCommas($this.closest('.board').find('.income').text())),
			        	cityExpense = $this.closest('.board').find('.expense'),
		        		origCityExpense = parseInt($this.closest('.board').find('.original').data('original')),
			        	oldCityExpense = parseInt(noCommas(cityExpense.text())),
			        	cityNet = $this.closest('.board').find('.net'),
	        			oldStateExpense = parseInt(noCommas($('.state .expense span').text())),
	        			stateIncome = parseInt(noCommas($('.state .income span').text())),
	        			stateNet = $('.state .net span'),
		        		original = parseInt(noCommas($this.attr('placeholder')));

		        	console.log(cityExpense);	

		        	if ($this.hasClass('f')) {
		        		oldCityExpense = origCityExpense;
		        		oldStateExpense = origStateExpense;
		        	}	

		        	var keepAtIt = setInterval(function(){
		        		var newValue = $this.attr('value');
		        		if (newValue.length == 0) { 
							newValue = original; 
						} else { 
							newValue = parseInt(newValue);
						}

						var	newCityValue = oldCityExpense + newValue - original;
						var	newStateValue = oldStateExpense + newValue - original;

						// update city expense
						cityExpense.text(addCommas(newCityValue));
						// update city net
						cityNet.text(addCommas(cityIncome - newCityValue));
						// update state expense
						stateExpense.text(addCommas(newStateValue));
						// update state net
						stateNet.text(addCommas(stateIncome - newStateValue));
		        	}, 100);

		        	// stop updating when the input loses focus
		        	$this.blur(function(){
		        		clearInterval(keepAtIt);
		        	});
		        }
		    });

		})(jQuery);

		$('#submit').click(function(){
			var cash = parseInt(noCommas($('#user-cash').text())),
				net = parseInt(noCommas($('.state .net span').text()));

			if (cash + net < 0) {
				$('.warning').remove();
				$(this).after('<h3 class="warning">If the budget remains where it is now, the state will go bankrupt. You need to cut funding to some cities, or find another way to come up with the cash.</h3>');
				return false;
			}
		});

		input.focus(function(){
			$this = $(this);
			$this.updateTotal();
			$this.addClass('f');
		});
	});
</script>

<?php 
} // end does user have cities

// if user is not logged in
} else { ?>

<div class="container">
	<div class="header"></div>
</div>

<?php }
get_footer(); ?>