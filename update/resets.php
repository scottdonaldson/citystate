<?php

$resets = array( 
	'downgrade-n_0',	// Neighborhood downgrades to level 0
	'downgrade-n_1'		// Neighborhood downgrades to level 1
);

foreach ($resets as $reset) {
	update_post_meta($ID, $reset, 0);
}


?>