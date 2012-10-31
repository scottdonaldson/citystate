<?php

$resets = array( 'warning' );

foreach ($resets as $reset) {
	update_post_meta($ID, $reset, 0);
}


?>