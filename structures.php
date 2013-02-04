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

	// Education
	'library' 		=> array( 	   'library', 	   'libraries', 1,  300,  100, 0,  1000,  3,  5, 15),
	'college'		=> array(	   'college',	    'colleges', 1, 1000,  500, 0,  5000,  5, 10, 20),
	'university' 	=> array( 	'university',   'universities', 1, 2000, 1000, 0,  8000,  8, 20, 30),

	// Culture
	'cinema' 		=> array( 		'cinema', 	 	 'cinemas', 1,  450,  200, 0,  2000,  5, 10,  5),
	'stadium'		=> array(	   'stadium',       'stadiums', 1, 2500, 1000, 0, 12000, 10, 25,  0),

	// Resource-related
	'farm'			=> array( 		  'farm',		   'farms', 0,  250,  100, 0,     0,  0,  0,  0),
	'pasture'		=> array(	   'pasture',		'pastures', 0,  250,  100, 0,     0,  0,  0,  0),
	'lumberyard'    => array(	'lumberyard',	 'lumberyards', 0,  250,  100, 0,     0,  0,  0,  0),
	'fishery'		=> array(	   'fishery',	   'fisheries', 0,  300,  100, 0,     0,  0,  0,  0),
	'quarry'		=> array(		'quarry',		'quarries', 0,  500,  100, 0,     0,  0,  0,  0),
	'coal_mine'		=> array(	 'coal mine',	  'coal mines', 0,  500,  100, 0,	  0,  0,  0,  0),
	'iron_mine'		=> array(	 'iron mine',	  'iron mines', 0,  500,  100, 0,	  0,  0,  0,  0),
	'gold_mine'		=> array(	 'gold mine',	  'gold mines', 0,  500,  100, 0,	  0,  0,  0,  0),
	'uranium_mine'	=> array( 'uranium mine',  'uranium mines', 0,  500,  100, 0,	  0,  0,  0,  0),
	'oil_rig'		=> array(	   'oil rig',	    'oil rigs', 0,  700,  100, 0,	  0,  0,  0,  0),
	// 'business'		=> array(	  'business',	  'businesses', 0,  300,    0, 0,     0,  0,  0,  0),
	// 'factory' 		=> array(	   'factory', 	   'factories', 0,  500,  100, 0,     0,  0,  0,  0),
	
	'port'			=> array(		  'port',	 	   'ports', 6,  800,  150, 0,  4000,  0,  5,  0),
	
	'city_hall'     => array(    'city hall',     'city halls', 1, 1000,    0, 0, 10000,  5,  5,  5),	
	// 'train_station' => array('train station', 'train stations', 1, 1800,  300, 0, 10000,  0, 10,  0),
	
);

?>