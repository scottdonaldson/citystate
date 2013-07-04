<?php
/*
Template Name: Secondo 3 bulk upload
*/
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Secondo 3 bulk upload</title>

<?php

$map = array(
	1 => array(
		  	'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('sand', array( 'fish' => 5 )), 
		  	'4' => array('sand', array( 'fish' => 1 )), 
		  	'5' => array('grass', array( 'arable_land' => 5, 'stone' => 1 )), 
		  	'6' => array('hills', array( 'stone' => 6, 'iron' => 3 )), 
		  	'7' => array('grass', array( 'sheep' => 8, 'arable_land' => 2 )), 
		  	'8' => array('grass', array( 'sheep' => 8, 'arable_land' => 5 )), 
		  	'9' => array('hills', array( 'stone' => 5, 'gold' => 1 )), 
		  	'10' => array('hills', array( 'coal' => 6, 'fish' => 5 )), 
		),
	2 => array(
			'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('water', ''), 
		  	'4' => array('grass', array( 'arable_land' => 6, 'fish' => 6 )), 
		  	'5' => array('grass', array( 'arable_land' => 5, 'sheep' => 2 )), 
		  	'6' => array('grass', array( 'sheep' => 10 )), 
		  	'7' => array('hills', array( 'coal' => 3 )), 
		  	'8' => array('mountains', array( 'stone' => 7, 'uranium' => 5, 'coal' => 3 )), 
		  	'9' => array('mountains', array( 'stone' => 9, 'iron' => 7 )), 
		  	'10' => array('sand', array( 'stone' => 1 )), 
		),
	3 => array(
		  	'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('water', ''), 
		  	'4' => array('water', ''), 
		  	'5' => array('water', ''), 
		  	'6' => array('water', ''), 
		  	'7' => array('sand', array( 'fish' => 4 )), 
		  	'8' => array('hills', array( 'coal' => 5, 'fish' => 1 )), 
		  	'9' => array('mountains', array( 'iron' => 8, 'stone' => 5 )),
		  	'10' => array('mountains', array( 'iron' => 10, 'gold' => 2 )),
		),
	4  => array(
		  	'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('water', ''), 
		  	'4' => array('water', ''), 
		  	'5' => array('water', ''), 
		  	'6' => array('water', ''), 
		  	'7' => array('water', ''), 
		  	'8' => array('sand', array( 'stone' => 2, 'fish' => 2 )), 
		  	'9' => array('mountains', array( 'iron' => 5, 'gold' => 5, 'coal' => 5, 'stone' => 5, 'uranium' => 1 )), 
		  	'10' => array('hills', array( 'uranium' => 4, 'fish' => 3 )), 
		),
	5  => array(
			'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('water', ''), 
		  	'4' => array('water', ''), 
		  	'5' => array('water', ''), 
		  	'6' => array('water', ''), 
		  	'7' => array('water', ''), 
		  	'8' => array('hills', array( 'coal' => 8, 'fish' => 2 )), 
		  	'9' => array('hills', array( 'coal' => 7, 'stone' => 3, 'fish' => 3 )), 
		  	'10' => array('water', ''), 
		),
	6  => array(
		  	'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('water', ''), 
		  	'4' => array('water', ''), 
		  	'5' => array('water', ''), 
		  	'6' => array('water', ''), 
		  	'7' => array('forest', array( 'lumber' => 6, 'fish' => 4 )), 
		  	'8' => array('hills', array( 'coal' => 5, 'stone' => 5, 'fish' => 1 )), 
		    '9' => array('forest', array( 'lumber' => 7, 'fish' => 2 )), 
		  	'10' => array('water', ''), 	
		),
	7  => array(
		  	'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('water', ''), 
		  	'4' => array('grass', array( 'fish' => 10, 'sheep' => 7, 'arable_land' => 2 )), 
		  	'5' => array('grass', array( 'sheep' => 6, 'arable_land' => 4, 'fish' => 3 )), 
		  	'6' => array('grass', array( 'sheep' => 5, 'lumber' => 2, 'fish' => 1 )), 
		  	'7' => array('forest', array( 'lumber' => 8 )), 
		  	'8' => array('forest', array( 'lumber' => 6, 'coal' => 1 )), 
		  	'9' => array('grass', array( 'arable_land' => 10, 'lumber' => 5, 'fish' => 2 )), 
		  	'10' => array('grass', array( 'arable_land' => 8, 'fish' => 3 )), 
		),
	8  => array(
		  	'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('water', ''), 
		  	'4' => array('water', ''), 
		  	'5' => array('grass', array( 'sheep' => 6, 'fish' => 5 )), 
		  	'6' => array('forest', array( 'sheep' => 5, 'lumber' => 3, 'fish' => 2 )), 
		  	'7' => array('grass', array( 'sheep' => 6, 'arable_land' => 5, 'fish' => 2 )), 
		  	'8' => array('forest', array( 'lumber' => 7 )), 
		  	'9' => array('grass', array( 'arable_land' => 8, 'lumber' => 2 )), 
		  	'10' => array('grass', array( 'arable_land' => 8, 'sheep' => 5, 'fish' => 3 )), 
		),
	9  => array(
			'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('water', ''), 
		  	'4' => array('water', ''), 
		  	'5' => array('water', ''), 
		  	'6' => array('water', ''), 
		  	'7' => array('water', ''), 
		  	'8' => array('grass', array( 'fish' => 8, 'arable_land' => 5, 'sheep' => 5 )), 
		  	'9' => array('sand', array( 'fish' => 7, 'oil' => 2 )), 
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
$ID = get_page_by_title('Secondo 3', OBJECT, 'region');
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
	update_post_meta($ID, 'POS-x', '2');
	update_post_meta($ID, 'POS-y', '2');
?>

</head>
<body>
	<p>Secondo 3 updated.</p>
	<a href="<?= home_url(); ?>?region=secondo-3">Back to Secondo 3</a>
</body>
</html>