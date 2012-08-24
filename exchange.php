<?php

$cash_change = $_POST['cashChange']; 

global $current_user;
get_currentuserinfo();

$cash_current = get_field('cash','user_'.$current_user->ID);

update_field('cash',$cash_current+$cash_change,'user_'.$current_user->ID);

?>