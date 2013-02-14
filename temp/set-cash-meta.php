<!DOCTYPE html>
<html>
<?php
/*
Template Name: Cash User Meta from Field
*/

$users = get_users();
foreach($users as $user) {
	$cash = get_field('cash', 'user_'.$user->ID);
	update_user_meta($user->ID, 'cash', $cash);
}

$alert = '<p>Cash meta updated. Back to <a href="'.home_url().'">main map</a>.</p>';
?>

<body>
	<?php if ($alert) { ?>
		<div id="alert"><?php echo $alert; ?></div>
	<?php } ?>	
</body>

</html>