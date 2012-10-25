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
	 *						population at which desired
	 *						happiness increase (percentage)
	 *						)
	 */
	'park'			=> array( 		  'park', 		  'parks', 0,   50,   50, 0,     0,  5),
	'neighborhood' 	=> array( 'neighborhood', 'neighborhoods', 0,  100,  100, 2,     0,  2),
	'library' 		=> array( 	   'library', 	  'libraries', 1,  300,  100, 0,  1000, 10),
	'cinema' 		=> array( 		'cinema', 		'cinemas', 1,  450,  200, 0,  2500, 15),
	// 'port'			=> array(		  'port',		  'ports', 1,  800,  250, 0,  true),
	'university' 	=> array( 	'university',  'universities', 1, 1000,  500, 0,  8000, 20),
	'stadium'		=> array(	   'stadium',      'stadiums', 1, 2500, 1000, 0, 12000, 25),
)

?>