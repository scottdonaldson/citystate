<?php

function get_funding_level($ID, $structure) {

	$pop = get_post_meta($ID, 'population', true);

	// Determined differently for structures there are multiples of
	$multiples = array('park', 'farm', 'fishery', 'lumberyard', 'port');

	if ($structure['max'] == 1) {
		if ($pop >= $structure['desired']) {
			$need_funding = round(0.02 * $structure['cost'] * (1 + 0.1 * ($pop - $structure['desired']) / 1000) );
		} else {
			$need_funding = 0.02 * $structure['cost'];
		}
	} elseif (in_array($structure, $multiples)) { 
		$count = meta($structure.'s');

		$need_funding = $count * round(0.02 * $structure['cost'] * (1 + 0.1 * (($pop - $structure['desired']) / 1000) ));
	}
	// Based on the ratio of the new funding to the needed funding,
	// return a qualitative value for funding
	$diff = $newfund / $need_funding;
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

function update_funding($ID, $structure, $basename) {

	if (isset($_POST[$basename.'-'.$structure]) &&
		$_POST[$basename.'-'.$structure] != '') {

		$newfund = $_POST[$basename.'-'.$structure];
		update_post_meta($ID, 'funding-'.$structure, $newfund);
	}

}

function show_budget_module($current_user, $ID) {

	$link = get_permalink($ID);
	$pop = meta('population') ? meta('population') : 0; 
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
		foreach (get_structures() as $structure) {

			get_funding_level($ID, $structure);

			// Determine placeholder value. If funding is set, then that funding,
			// otherwise the minimum of .02 times the cost to build
			if (meta('funding-'.$structure)) {
				$placeholder = get_post_meta($ID, 'funding-'.$structure, true);
			} else {
				// Nonrepeating
				if ($max == 1) {
					$placeholder = 0.02 * $cost; 
				// Repeating
				} else {
					$count = get_post_meta($ID, $structure.'s', true);
					$placeholder = 0.02 * $cost * $count; 
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
						<label for="<?php echo basename($link).'-'.$structure; ?>"><?php echo ucfirst($name); ?><small> (Min: <?php echo 0.02 * $cost; ?>)</small></label>
						<input type="number" id="<?php echo basename($link).'-'.$structure; ?>" name="<?php echo basename($link).'-'.$structure; ?>" max="<?php echo 10*$need_funding_[$structure]; ?>" min="<?php echo .02*$cost; ?>" placeholder="<?php echo th($placeholder_[$structure]); ?>" class="funding-<?php echo $funding; ?>" />
					</div><!-- just a clearfix -->
				<?php }

			// Some structures require funding but can build multiple
			$multiples = array('park', 'farm', 'fishery', 'lumberyard', 'port');
			} elseif (in_array($structure['slug'], $multiples)) { 
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
				foreach (get_structures() as $structure) {
					
					if ($max == 1) {
						if (get_post_meta($ID, $structure.'-y', true) != 0) { 
							$expenses_[$ID] += $placeholder_[$structure];
						}
					} /* TODO:
					elseif (in_array($structure, $multiples)) {
						if (get_post_meta($ID, $structure.'s', true) > 0) {
							$expenses_[$ID] += $placeholder_[$structure];
						}
					} */
				}
			?></p>
			<p class="alignright expense"><?php echo th($expenses_[$ID]); ?></p>

			<strong class="alignleft">City Net:</strong>
			<strong class="alignright net"><?php echo th($income_[$ID] - $expenses_[$ID]); ?></strong>
		</section>

		<div class="original hidden" data-original="<?php echo $expenses_[$ID]; ?>"></div>
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