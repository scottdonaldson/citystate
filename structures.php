<?php

$structures = array(
	/* 
	'slug' 			=> array(
	 *						'Singular Name',
	 *						'Plural Name',
	 *						max. in one city (or 0 if no limit)
 	 *						cost,
 	 *						max. pop increase,
	 *						levels of upgrade,
	 *						)
	 */
	'park'			=> array( 		  'park', 		  'parks', 0,   50,  50, 0),
	'neighborhood' 	=> array( 'neighborhood', 'neighborhoods', 0,  100, 100, 2),
	'library' 		=> array( 	   'library', 	  'libraries', 1,  300, 100, 0),
	'cinema' 		=> array( 		'cinema', 		'cinemas', 1,  450, 200, 0),
	'university' 	=> array( 	'university',  'universities', 1, 1000, 500, 0),
)

?>