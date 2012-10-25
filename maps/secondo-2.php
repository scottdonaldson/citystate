<?php

$map = array(
	/* 
	row number => array(value for each tile: 0 water, 1 land)
	 */
	1  => array(0, 0, 0, 0, 0, 1, 0, 0, 0, 0),
	2  => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	3  => array(1, 1, 0, 0, 0, 0, 1, 1, 0, 0),
	4  => array(1, 1, 1, 1, 1, 0, 0, 0, 0, 0),
	5  => array(1, 1, 1, 1, 1, 1, 1, 0, 0, 0),
	6  => array(1, 1, 1, 1, 1, 1, 0, 0, 0, 0),
	7  => array(0, 1, 1, 1, 1, 1, 0, 0, 0, 0),
	8  => array(0, 0, 1, 1, 1, 0, 0, 0, 0, 0),
	9  => array(0, 0, 1, 1, 0, 0, 0, 0, 0, 0),
	10 => array(0, 1, 1, 1, 1, 0, 0, 0, 0, 0),
);
$neighbors = array(
	'nw' => 0,
	'n'  => 0,
	'ne' => 0,
	'w'  => 'secondo-1',
	'e'  => 0,
	'sw' => 'secondo-3',
	's'  => 'secondo-4',
	'se' => 0,
);

?>