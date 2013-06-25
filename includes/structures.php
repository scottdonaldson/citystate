<?php

$structures = array();
function set_structure_values($slug, $name, $plural, $values) {
	$structures[$slug] = array( 
		'name'    => $name, 
		'plural'  => $plural,
		'max'     => $values[0],
		'cost'    => $values[1],
		'target'  => $values[2],
		'upgrade' => $values[3],
		'desired' => $values[4],
		'happy'   => $values[5],
		'culture' => $values[6],
		'edu'     => $values[7]
	);
}
set_structure_values('park', 'park', 'parks', array(0, 50, 50, 0, 200, 1, 2, 0));
set_structure_values('neighborhood', 'neighborhood', 'neighborhoods', array(0, 100, 100, 2, 0, 0, 0, 0));

// Education
set_structure_values('library', 'library', 'libraries', array(1, 300, 100, 0, 1000, 3, 5, 15));
set_structure_values('college', 'college', 'colleges', array(1, 1000, 500, 0, 5000, 5, 10, 20));
set_structure_values('university', 'university', 'universities', array(1, 2000, 1000, 0, 8000, 8, 20, 30));

// Culture
set_structure_values('cinema', 'cinema', 'cinemas', array(1, 450, 200, 0, 2000, 5, 10, 5));
set_structure_values('stadium', 'stadium', 'stadiums', array(1, 2500, 1000, 0, 12000, 10, 25, 0));

// Resource
set_structure_values('farm', 'farm', 'farms', array(0, 250, 100));
set_structure_values('pasture', 'pasture', 'pastures', array(0, 250, 100));
set_structure_values('lumberyard', 'lumberyard', 'lumberyard', array(0, 250, 100));
set_structure_values('fishery', 'fishery', 'fisheries', array(0, 300, 100));
set_structure_values('quarry', 'quarry', 'quarries', array(0, 500, 100));
set_structure_values('coal_mine', 'coal mine', 'coal mines', array(0, 500, 100));
set_structure_values('iron_mine', 'iron mine', 'iron mines', array(0, 500, 100));
set_structure_values('gold_mine', 'gold mine', 'gold mines', array(0, 500, 100));
set_structure_values('uranium_mine', 'uranium mine', 'uranium mines', array(0, 500, 100));
set_structure_values('oil_rig', 'oil rig', 'oil rigs', array(0, 700, 100));

// Misc/trade/civic
set_structure_values('port', 'port', 'ports', array(6, 800, 150, 0, 4000, 0, 5, 0));
set_structure_values('city_hall', 'city hall', 'city halls', array(1, 1000, 0, 0, 10000, 5, 5, 5));

?>