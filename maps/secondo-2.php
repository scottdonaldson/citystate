<?php
/*
Template Name: Secondo 2 bulk upload
*/
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Secondo 2 bulk upload</title>


<?php

$map = array(
	1 => array(
		  	'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('water', ''), 
		  	'4' => array('water', ''), 
		  	'5' => array('water', ''), 
		  	'6' => array('grass', array('uranium' => 6, 'arable_land' => 5, 'fish' => 5, 'sheep' => 3 )), 
		  	'7' => array('water', ''), 
		  	'8' => array('water', ''), 
		  	'9' => array('water', ''), 
		  	'10' => array('water', ''), 
		),
	2 => array(
			'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('water', ''), 
		  	'4' => array('water', ''), 
		  	'5' => array('water', ''), 
		  	'6' => array('water', ''), 
		  	'7' => array('water', ''), 
		  	'8' => array('water', ''), 
		  	'9' => array('water', ''), 
		  	'10' => array('water', ''), 
		),
	3 => array(
		  	'1' => array('grass', array( 'sheep' => 5, 'lumber' => 3 )), 
		  	'2' => array('sand', array( 'fish' => 4 )), 
		  	'3' => array('water', ''), 
		  	'4' => array('water', ''), 
		  	'5' => array('water', ''), 
		  	'6' => array('water', ''), 
		  	'7' => array('mountains', array( 'uranium' => 8, 'iron' => 6, 'stone' => 6, 'fish' => 5 )), 
		  	'8' => array('hills', array( 'stone' => 7, 'coal' => 7, 'fish' => 5 )), 
		  	'9' => array('water', ''),
		  	'10' => array('water', ''),
		),
	4  => array(
		  	'1' => array('forest', array( 'lumber' => 7, 'fish' => 1 )), 
		  	'2' => array('grass', array( 'sheep' => 7, 'arable_land' => 3 )), 
		  	'3' => array('forest', array( 'fish' => 5, 'lumber' => 4 )), 
		  	'4' => array('grass', array( 'arable_land' => 7, 'coal' => 1 )), 
		  	'5' => array('grass', array( 'sheep' => 5, 'arable_land' => 3, 'fish' => 3 )), 
		  	'6' => array('water', ''), 
		  	'7' => array('water', ''), 
		  	'8' => array('water', ''), 
		  	'9' => array('water', ''), 
		  	'10' => array('water', ''), 
		),
	5  => array(
			'1' => array('forest', array( 'lumber' => 8 )), 
		  	'2' => array('forest', array( 'lumber' => 10, 'stone' => 2 )), 
		  	'3' => array('hills', array( 'coal' => 7, 'iron' => 5 )), 
		  	'4' => array('grass', array( 'arable_land' => 5 )), 
		  	'5' => array('grass', array( 'arable_land' => 5, 'fish' => 1 )), 
		  	'6' => array('sand', array( 'fish' => 3 )), 
		  	'7' => array('sand', array( 'fish' => 10 )), 
		  	'8' => array('water', ''), 
		  	'9' => array('water', ''), 
		  	'10' => array('water', ''), 
		),
	6  => array(
		  	'1' => array('hills', array( 'stone' => 5, 'coal' => 3 )), 
		  	'2' => array('hills', array( 'coal' => 7, 'iron' => 4 )), 
		  	'3' => array('mountains', array( 'stone' => 8, 'iron' => 7 )), 
		  	'4' => array('forest', array( 'lumber' => 10, 'stone' => 4 )), 
		  	'5' => array('sand', array( 'stone' => 3 )), 
		  	'6' => array('sand', array( 'fish' => 5 )), 
		  	'7' => array('water', ''), 
		  	'8' => array('water', ''), 
		    '9' => array('water', ''), 
		  	'10' => array('water', ''), 	
		),
	7  => array(
		  	'1' => array('water', ''), 
		  	'2' => array('hills', array( 'coal' => 6, 'uranium' => 2, 'fish' => 1 )), 
		  	'3' => array('mountains', array( 'coal' => 7, 'iron' => 7, 'gold' => 1 )), 
		  	'4' => array('hills', array( 'iron' => 7, 'stone' => 2 )), 
		  	'5' => array('sand', array( 'stone' => 1, 'fish' => 1 )), 
		  	'6' => array('sand', array( 'fish' => 6 )), 
		  	'7' => array('water', ''), 
		  	'8' => array('water', ''), 
		  	'9' => array('water', ''), 
		  	'10' => array('water', ''), 
		),
	8  => array(
		  	'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('mountains', array( 'iron' => 8, 'stone' => 4 )), 
		  	'4' => array('mountains', array( 'stone' => 7, 'oil' => 3 )), 
		  	'5' => array('sand', array( 'fish' => 6 )), 
		  	'6' => array('water', ''), 
		  	'7' => array('water', ''), 
		  	'8' => array('water', ''), 
		  	'9' => array('water', ''), 
		  	'10' => array('water', ''), 
		),
	9  => array(
			'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('mountains', array( 'iron' => 5, 'coal' => 5, 'stone' => 5 )), 
		  	'4' => array('hills', array( 'coal' => 6, 'uranium' => 4, 'fish' => 4 )), 
		  	'5' => array('water', ''), 
		  	'6' => array('water', ''), 
		  	'7' => array('water', ''), 
		  	'8' => array('water', ''), 
		  	'9' => array('water', ''), 
		  	'10' => array('water', ''), 
		),
	10 => array(
		  	'1' => array('water', ''), 
		  	'2' => array('hills', array( 'fish' => 5, 'iron' => 1 )), 
		  	'3' => array('mountains', array( 'iron' => 10, 'uranium' => 3 )), 
		  	'4' => array('hills', array( 'coal' => 4, 'lumber' => 2 )), 
		  	'5' => array('hills', array( 'coal' => 4, 'fish' => 3 )), 
		  	'6' => array('water', ''), 
		  	'7' => array('water', ''), 
		  	'8' => array('water', ''), 
		  	'9' => array('water', ''), 
		  	'10' => array('water', ''), 
		),
);
$ID = get_page_by_title('Secondo 2', OBJECT, 'region');
	$ID = $ID->ID;
	foreach ($map as $y => $row) {
		foreach ($row as $x => $tile) {
			update_post_meta($ID, $x.','.$y.'-terrain', $tile[0]);

			foreach ($tile[1] as $resource => $value) {
				update_post_meta($ID, $x.','.$y.'-'.$resource, $value);
			}
		}
	}
	update_post_meta($ID, 'POS-x', '3');
	update_post_meta($ID, 'POS-y', '1');
?>

</head>
<body>
	<p>Secondo 2 updated.</p>
	<a href="<?= home_url(); ?>?region=secondo-2">Back to Secondo 2</a>
</body>
</html>