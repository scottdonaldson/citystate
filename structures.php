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
	 *						culture increase (percentage)
	 *						education increase (percentage)
	 *						)
	 */
	'park'			=> array( 		  'park', 		   'parks', 0,   50,   50, 0,   200,  1,  2,  0),
	'neighborhood' 	=> array( 'neighborhood',  'neighborhoods', 0,  100,  100, 2,     0,  0,  0,  0),
	'library' 		=> array( 	   'library', 	   'libraries', 1,  300,  100, 0,  1000,  3,  5, 15),
	'cinema' 		=> array( 		'cinema', 	 	 'cinemas', 1,  450,  200, 0,  2000,  5, 10,  5),
	'port'			=> array(		  'port',	 	   'ports', 6,  800,  150, 0,  4000,  0,  5,  0),
	'university' 	=> array( 	'university',   'universities', 1, 1000,  500, 0,  8000,  8, 20, 30),
	// 'city_hall'     => array(    'city hall',     'city halls', 1, 1000,    0, 0, 10000,  5,  5,  5),	
	// 'train_station' => array('train station', 'train stations', 1, 1800,  300, 0, 10000,  0, 10,  0),
	'stadium'		=> array(	   'stadium',       'stadiums', 1, 2500, 1000, 0, 12000, 10, 25,  0),
)

?>