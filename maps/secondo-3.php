<?php

$map = array(
	/* 
	row number => array(value for each tile: 0 water, 1 land)
	 */
	1  => array(0, 0, 1, 1, 1, 1, 1, 1, 1, 1),
	2  => array(0, 0, 0, 1, 1, 1, 1, 1, 1, 1),
	3  => array(0, 0, 0, 0, 0, 0, 1, 1, 1, 1),
	4  => array(0, 0, 0, 0, 0, 0, 0, 1, 1, 1),
	5  => array(0, 0, 0, 0, 0, 0, 0, 1, 1, 0),
	6  => array(0, 0, 0, 0, 0, 0, 1, 1, 1, 0),
	7  => array(0, 0, 0, 1, 1, 1, 1, 1, 1, 1),
	8  => array(0, 0, 0, 0, 1, 1, 1, 1, 1, 1),
	9  => array(0, 0, 0, 0, 0, 0, 0, 1, 1, 0),
	10 => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
);
$neighbors = array(
	'nw' => 'originalia',
	'n'  => 'secondo-1',
	'ne' => 'secondo-2',
	'w'  => 0,
	'e'  => 'secondo-4',
	'sw' => 0,
	's'  => 0,
	'se' => 0,
);

?>