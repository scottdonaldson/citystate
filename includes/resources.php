<?php 
function get_resources() {
	$resources = array(
		/*
		'resource slug' => array('name', 'finished product', 'structure')
		*/
		'fish' => array('fish', 'fish_stock', 'fishery'), 
		'arable_land' => array('arable land', 'food_stock', 'farm'), 
		'sheep' => array('sheep', 'wool_stock', 'pasture'), 
		'lumber' => array('lumber', 'lumber_stock', 'lumberyard'), 
		'coal' => array('coal', 'coal_stock', 'coal mine'), 
		'iron' => array('iron', 'iron_stock', 'iron mine'), 
		'oil' => array('oil', 'oil_stock', 'oil rig'), 
		'uranium' => array('uranium', 'uranium_stock', 'uranium mine'), 
		'stone' => array('stone', 'stone_stock', 'quarry'), 
		'gold' => array('gold', 'gold_stock', 'gold mine')
		); 
	return $resources;
}
?>