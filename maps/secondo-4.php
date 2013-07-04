<?php
/*
Template Name: Secondo 4 bulk upload
*/
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Secondo 4 bulk upload</title>


<?php

$map = array(
	1 => array(
		  	'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('hills', array( 'iron' => 8, 'coal' => 4 )), 
		  	'4' => array('grass', array( 'sheep' => 2 )), 
		  	'5' => array('forest', array( 'lumber' => 6, 'fish' => 3 )), 
		  	'6' => array('water', ''), 
		  	'7' => array('water', ''), 
		  	'8' => array('water', ''), 
		  	'9' => array('water', ''), 
		  	'10' => array('water', ''), 
		),
	2 => array(
			'1' => array('sand', array( 'fish' => 4 )), 
		  	'2' => array('water', array(  )), 
		  	'3' => array('hills', array( 'iron' => 5, 'stone' => 3, 'fish' => 1 )), 
		  	'4' => array('forest', array( 'lumber' => 8 )), 
		  	'5' => array('forest', array( 'lumber' => 5, 'fish' => 2 )), 
		  	'6' => array('grass', array( 'fish' => 9 )), 
		  	'7' => array('water', ''), 
		  	'8' => array('water', ''), 
		  	'9' => array('water', ''), 
		  	'10' => array('water', ''), 
		),
	3 => array(
		  	'1' => array('mountains', array( 'iron' => 9 )), 
		  	'2' => array('hills', array( 'iron' => 5, 'fish' => 3, 'coal' => 3 )), 
		  	'3' => array('mountains', array( 'iron' => 9, 'stone' => 5, 'uranium' => 2 )), 
		  	'4' => array('hills', array( 'coal' => 5, 'iron' => 5, 'fish' => 1 )), 
		  	'5' => array('water', ''), 
		  	'6' => array('water', ''), 
		  	'7' => array('water', ''), 
		  	'8' => array('water', ''), 
		  	'9' => array('water', ''),
		  	'10' => array('water', ''),
		),
	4  => array(
		  	'1' => array('mountains', array( 'iron' => 8, 'stone' => 3 )), 
		  	'2' => array('mountains', array( 'iron' => 7, 'stone' => 5 )), 
		  	'3' => array('forest', array( 'lumber' => 7, 'stone' => 1 )), 
		  	'4' => array('sand', array( 'fish' => 3 )), 
		  	'5' => array('sand', array( 'fish' => 7 )), 
		  	'6' => array('water', ''), 
		  	'7' => array('sand', array( 'fish' => 10, 'stone' => 5 )), 
		  	'8' => array('water', ''), 
		  	'9' => array('water', ''), 
		  	'10' => array('water', ''), 
		),
	5  => array(
			'1' => array('sand', array( 'fish' => 3, 'iron' => 3 )), 
		  	'2' => array('hills', array( 'coal' => 7, 'iron' => 5, 'uranium' => 1 )), 
		  	'3' => array('forest', array( 'lumber' => 8, 'fish' => 2 )), 
		  	'4' => array('water', ''), 
		  	'5' => array('water', ''), 
		  	'6' => array('water', ''), 
		  	'7' => array('water', ''), 
		  	'8' => array('water', ''), 
		  	'9' => array('water', ''), 
		  	'10' => array('water', ''), 
		),
	6  => array(
		  	'1' => array('water', ''), 
		  	'2' => array('sand', array( 'fish' => 1 )), 
		  	'3' => array('sand', array( 'fish' => 6, 'lumber' => 2 )), 
		  	'4' => array('water', ''), 
		  	'5' => array('water', ''), 
		  	'6' => array('water', ''), 
		  	'7' => array('water', ''), 
		  	'8' => array('water', ''), 
		    '9' => array('hills', array( 'fish' => 8, 'gold' => 3, 'uranium' => 2 )), 
		  	'10' => array('water', ''), 	
		),
	7  => array(
		  	'1' => array('grass', array( 'arable_land' => 7, 'sheep' => 5, 'fish' => 2 )), 
		  	'2' => array('grass', array( 'arable_land' => 9, 'sheep' => 8, 'fish' => 2 )), 
		  	'3' => array('water', ''), 
		  	'4' => array('water', ''), 
		  	'5' => array('water', ''), 
		  	'6' => array('hills', array( 'uranium' => 7, 'gold' => 5, 'fish' => 5 )), 
		  	'7' => array('mountains', array( 'uranium' => 7, 'stone' => 4, 'gold' => 3 )), 
		  	'8' => array('mountains', array( 'gold' => 10 )), 
		  	'9' => array('hills', array( 'gold' => 7, 'uranium' => 5, 'fish' => 3 )), 
		  	'10' => array('water', ''), 
		),
	8  => array(
		  	'1' => array('grass', array( 'sheep' => 6, 'arable_land' => 5, 'fish' => 1 )), 
		  	'2' => array('grass', array( 'sheep' => 7, 'fish' => 3 )), 
		  	'3' => array('water', ''), 
		  	'4' => array('water', ''), 
		  	'5' => array('grass', array( 'uranium' => 4 )), 
		  	'6' => array('hills', array( 'gold' => 3, 'uranium' => 3, 'fish' => 2 )), 
		  	'7' => array('mountains', array( 'uranium' => 6, 'fish' => 3 )), 
		  	'8' => array('hills', array( 'uranium' => 5, 'gold' => 2, 'fish' => 2 )), 
		  	'9' => array('water', ''), 
		  	'10' => array('water', ''), 
		),
	9  => array(
			'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('water', ''), 
		  	'4' => array('water', ''), 
		  	'5' => array('water', ''), 
		  	'6' => array('water', ''), 
		  	'7' => array('water', ''), 
		  	'8' => array('hills', array( 'fish' => 5, 'gold' => 3, 'uranium' => 2 )), 
		  	'9' => array('water', ''), 
		  	'10' => array('water', ''), 
		),
	10 => array(
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
);
$ID = get_page_by_title('Secondo 4', OBJECT, 'region');
	$ID = $ID->ID;
	foreach ($map as $y => $row) {
		foreach ($row as $x => $tile) {
			if ($x == 10) { $x = 0; }
			update_post_meta($ID, $x.','.$y.'-terrain', $tile[0]);

			foreach ($tile[1] as $resource => $value) {
				update_post_meta($ID, $x.','.$y.'-'.$resource, $value);
			}
		}
	}
	update_post_meta($ID, 'POS-x', '3');
	update_post_meta($ID, 'POS-y', '2');
?>

</head>
<body>
	<p>Secondo 4 updated.</p>
	<a href="<?= home_url(); ?>?region=secondo-4">Back to Secondo 4</a>
</body>
</html>