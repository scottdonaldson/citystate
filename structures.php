<?php

$structures = array(
	/* 
	'name' 			=> array(
	 *						repeating? T/F,
 	 *						cost,
 	 *						max. pop increase,
	 *						education modifier,
	 *						culture modifier,
	 *						)
	 */
	'park'			=> array( true,   50,  50,	  0,    0),
	'neighborhood' 	=> array( true,  100, 100,    0,    0),
	'library' 		=> array(false,  300, 100, 1.05, 1.02),
	'cinema' 		=> array(false,  450, 200,    0,  1.2),
	'university' 	=> array(false, 1000, 500,  1.5, 1.05),
	
)

?>