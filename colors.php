<?php

$users = get_users();
echo '<style>';
foreach($users as $user) {
	get_userdata($user->ID);
	$color = get_field('color','user_'.$user->ID);
	echo '.user-'.$user->user_login.'{ background: '.$color.'; }';
}
echo '</style>';
?>