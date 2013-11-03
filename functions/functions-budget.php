<?php

function get_funding_level($diff) {
	if ($diff < 1) {
		return 'bad';
	} elseif ($diff < 1.5) {
		return 'fair';
	} elseif ($diff < 5) {
		return 'good';
	} elseif ($diff >= 5) {
		return 'excellent';
	}
}

function get_funding($ID, $structure) {

	$pop = get_post_meta($ID, 'population', true);
	// Cost is always related to 1/50 the cost to build
	$cost = 0.02 * $structure['cost'];

	if ($structure['max'] == 1) {
		if ($pop >= $structure['desired']) {
			$need_funding = round($cost * (1 + 0.1 * ($pop - $structure['desired']) / 1000) );
		} else {
			$need_funding = $cost;
		}
	} elseif ($structure != 'neighborhood' && $structure != 'park') { 
		$count = get_post_meta($ID, $structure.'-number', true);

		$need_funding = $count * round($cost * (1 + 0.1 * (($pop - $structure['desired']) / 1000) ));
	}
	// Based on the ratio of the new funding to the needed funding,
	// return a qualitative value for funding
	$diff = $need_funding > 0 ? $newfund / $need_funding : 10;
	return get_funding_level($diff);
}

function update_funding($ID, $structure, $basename) {

	if (isset($_POST[$basename.'-'.$structure]) &&
		$_POST[$basename.'-'.$structure] != '') {

		$newfund = $_POST[$basename.'-'.$structure];
		update_post_meta($ID, 'funding-'.$structure, $newfund);
	}

}

function show_budget_module($current_user, $ID) {

	$link = get_permalink($ID);
	$pop = get_post_meta($ID, 'population', true) ? get_post_meta($ID, 'population', true) : 0; 
	$city = get_the_title($ID);
	?>

	<div id="city-<?= $ID; ?>" class="board">
		<h3><a class="snapshot" href="<?= $link; ?>"><?= $city; ?></a> <small>(Pop: <?= $pop; ?>)</small></h3>
		
		<?php 
		// Show happiness face
		$happy = get_happiness(get_post_meta($ID, 'happiness', true)); ?>
		<small class="face <?= $happy['class']; ?>"><?= $happy['message']; ?></small>
		
		<div class="structures clearfix">
		<?php 
		$at_least_one = false;
		foreach (get_structures() as $structure) {

			// Cost is always related to 1/50 the cost to build
			$cost = 0.02 * $structure['cost'];

			// Determine placeholder value. If funding is set, then that funding,
			// otherwise the minimum of .02 times the cost to build (and maybe multiplied
			// by the number of that structure present, if multiple)
			if (get_post_meta($ID, 'funding-'.$structure['slug'], true)) {
				$placeholder = get_post_meta($ID, 'funding-'.$structure['slug'], true);
			} else {
				$placeholder = $structure['max'] == 1 ? $cost : $cost * get_post_meta($ID, $structure['slug'].'-number', true);
			}

			// Output labels and inputs for nonrepeating and some repeating structures
			if ($structure['max'] == 1) {
				if (get_post_meta($ID, $structure['slug'].'-y', true) > 0) { 
					// If so, then there is at least one structure
					$at_least_one = true; 

					// Determine if placeholder is bad, fair, good, or excellent based on value needed.
					// Value needed increases if population is beyond the desired level
					$need_funding = $pop >= $structure['desired'] ? round($cost * (1 + 0.1 * (($pop - $structure['desired']) / 1000) )) : $cost;
					$funding = get_funding_level($placeholder / $need_funding);
					?>
					<div class="clearfix">
						<label for="<?= basename($link).'-'.$structure; ?>"><?= $structure['name']; ?><small> (Min: <?= $cost; ?>)</small></label>
						<input type="number" id="<?= basename($link).'-'.$structure['slug']; ?>" name="<?= basename($link).'-'.$structure['slug']; ?>" max="<?= 10 * $need_funding; ?>" min="<?= $cost; ?>" placeholder="<?php echo th($placeholder_[$structure]); ?>" class="funding-<?= $funding; ?>" />
					</div><!-- just a clearfix -->
				<?php }

			// Some structures require funding but can build multiple
			} elseif ($structure['slug'] != 'neighborhood' && $structure['slug'] != 'park') { 
				$count = get_post_meta($ID, $structure['slug'].'-number', true);
				if ($count > 0) {
					$at_least_one = true;

					// Determine if placeholder is bad, fair, good, or excellent based on value needed.
					$need_funding = $count * round($cost * (1 + 0.1 * (($pop - $structure['desired']) / 1000) ));
					$funding = get_funding_level($placeholder / $need_funding); ?>
					<div class="clearfix">
						<label for="<?= basename($link).'-'.$structure['slug']; ?>">
							<?php if ($count == 1) { echo $structure['name']; } else { echo $count.' '.$structure['plural']; } ?><small> (Min: <?= $cost * $count; ?>)</small></label>
						<input type="number" id="<?= basename($link).'-'.$structure['slug']; ?>" name="<?= basename($link).'-'.$structure['slug']; ?>" max="<?= 10 * $need_funding; ?>" min="<?= $cost * $count; ?>" placeholder="<?= th($placeholder); ?>" class="funding-<?= $funding; ?>" />
					</div><!-- just a clearfix -->
				<?php }
			}
		} 

		// If there's no structures that require funding...
		if (!$at_least_one) {
			echo '<p>No structures in this city require funding.</p>';
		} ?>
		</div><!-- .structures -->
		
		<section>
			<p class="alignleft">City Income:</p>
			<p class="alignright income"><?php $income = th(ceil(0.05 * $pop)); echo $income; ?></p>

			<p class="alignleft">City Expenses: <?php
				$expenses = 0;
				foreach (get_structures() as $structure) {
					
					if ($structure['max'] == 1) {
						if (get_post_meta($ID, $structure.'-y', true) != 0) { 
							$expenses += $placeholder;
						}
					} elseif ($structure['slug'] != 'park' && $structure['slug'] != 'neighborhood') {
						if (get_post_meta($ID, $structure['slug'].'-number', true) > 0) {
							$expenses += $placeholder;
						}
					}
				}
			?></p>
			<p class="alignright expense"><?php echo th($expenses); ?></p>

			<strong class="alignleft">City Net:</strong>
			<strong class="alignright net"><?php echo th($income - $expenses); ?></strong>
		</section>

		<div class="original hidden" data-original="<?= $expenses; ?>"></div>
	</div>
	<?php
}

function show_state_module() { 
	if (get_user_meta($current_user->ID, 'budget_warning', true) == 1) { 
		$checked = 'checked'; 
	}
	?>
	<div class="state">
		<h3>State</h3>
		<input type="checkbox" id="budget_warning" name="budget_warning" <?= $checked ?> />
		<label class="grey" for="budget_warning">
			<small>Always warn me if state expenses are greater than state income.</small>
		</label>

		<div class="income">Total Income: <span><?= th($state_income); ?></span></div>

		<div class="original hidden" data-original="<?= $state_expenses; ?>"></div>
		<div class="expense">Total Expenses: <span><?= th($state_expenses); ?></span></div>

		<div class="net"><strong>Total Net: <span><?= th($state_income - $state_expenses); ?></span></strong></div>

	</div>
<?php
}

?>