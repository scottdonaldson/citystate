<?php

$structures = array(
	/* 
	'name' 			=> array(
	 *						repeating? T/F,
 	 *						cost,
 	 *						max. pop increase,
	 *						upgrade,
	 *						levels of upgrade,
	 *						)
	 */
	'park'			=> array( true,   50,  50, false, 0),
	'neighborhood' 	=> array( true,  100, 100,  true, 2),
	'library' 		=> array(false,  300, 100, false, 0),
	'cinema' 		=> array(false,  450, 200, false, 0),
	'university' 	=> array(false, 1000, 500, false, 0),
	
)

?>