<!DOCTYPE html>
<html>
<?php
/*
Template Name: Set Base Culture and Education
*/

// Define paths
define('MAIN', dirname(__FILE__) . '/');

$args = array(
	'posts_per_page' => -1,
);
$culture_query = new WP_Query($args); 

while ($culture_query->have_posts()) : $culture_query->the_post();

$ID = get_the_ID();

// Reset
update_post_meta($ID, 'culture', 0);
update_post_meta($ID, 'education', 0);

include ( MAIN . 'structures.php');
foreach ($structures as $structure => $values) {

	include ( MAIN . 'structures/values.php');

	// Non-repeating structures
	if ($max != 0) {
		// See if it's been built yet
		$built_y = get_post_meta($ID, $structure.'-y', true);

		// Only run if it's been built
		if ($built_y != 0) {
			$city_culture = get_post_meta($ID, 'culture', true);
			$new_culture = $city_culture + ceil($culture - $culture * $city_culture / 100);
			update_post_meta($ID, 'culture', $new_culture);

			$city_edu = get_post_meta($ID, 'education', true);
			$new_edu = $city_edu + ceil($edu - $edu * $city_edu / 100);
			update_post_meta($ID, 'education', $new_edu);
		}
	} else {
		// Get total
		$num = get_post_meta($ID, $structure.'s', true);

		// If there's at least 1...
		if ($num != 0) {
			
			// Do it for each of them
			for ($i = 1; $i <= $num; $i++) {
				$city_culture = get_post_meta($ID, 'culture', true);
				$new_culture = $city_culture + ceil($culture - $culture * $city_culture / 100);
				update_post_meta($ID, 'culture', $new_culture);

				$city_edu = get_post_meta($ID, 'education', true);
				$new_edu = $city_edu + ceil($edu - $edu * $city_edu / 100);
				update_post_meta($ID, 'education', $new_edu);
			}
		}
	}
}

endwhile;
wp_reset_postdata();

$alert = '<p>Culture and education set. Back to <a href="'.home_url().'">main map</a>.</p>';
?>

<body>
	<?php if ($alert) { ?>
		<div id="alert"><?php echo $alert; ?></div>
	<?php } ?>	
</body>

</html>