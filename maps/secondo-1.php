<?php
/*
Template Name: Secondo 1 bulk upload
*/
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Secondo 1 bulk upload</title>


<?php

$map = array(
	'1' => array(
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
	'2' => array(
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
	'3' => array(
		  	'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('water', ''), 
		  	'4' => array('water', ''), 
		  	'5' => array('water', ''), 
		  	'6' => array('water', ''), 
		  	'7' => array('water', ''), 
		  	'8' => array('sand', array( 'fish' => 8 )), 
		  	'9' => array('sand', array( 'fish' => 7, 'arable_land' => 1 )),
		  	'10' => array('water', ''),
		),
	'4'  => array(
		  	'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('water', ''), 
		  	'4' => array('water', ''), 
		  	'5' => array('water', ''), 
		  	'6' => array('water', ''), 
		  	'7' => array('grass', array( 'fish' => 7, 'arable_land' => 5 )), 
		  	'8' => array('grass', array( 'sheep' => 6, 'arable_land' => 5, 'fish' => 4 )), 
		  	'9' => array('grass', array( 'sheep' => 8, 'arable_land' => 5 )), 
		  	'10' => array('forest', array( 'lumber' => 9, 'sheep' => 2)), 
		),
	'5'  => array(
			'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('water', ''), 
		  	'4' => array('water', ''), 
		  	'5' => array('water', ''), 
		  	'6' => array('water', ''), 
		  	'7' => array('water', ''), 
		  	'8' => array('water', ''), 
		  	'9' => array('grass', array( 'arable_land' => 6, 'fish' => 6, 'sheep' => 2 )), 
		  	'10' => array('forest', array( 'fish' => 7, 'lumber' => 5 )), 
		),
	'6'  => array(
		  	'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('water', ''), 
		  	'4' => array('water', ''), 
		  	'5' => array('grass', array( 'fish' => 9, 'sheep' => 5 )), 
		  	'6' => array('sand', array( 'fish' => 9 )), 
		  	'7' => array('water', ''), 
		  	'8' => array('water', ''), 
		    '9' => array('water', ''), 
		  	'10' => array('grass', array( 'fish' => 6, 'sheep' => 3 )), 	
		),
	'7'  => array(
		  	'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('sand', array( 'fish' => 8, 'arable_land' => 1 )), 
		  	'4' => array('grass', array( 'fish' => 5, 'sheep' => 5, 'arable_land' => 3 )), 
		  	'5' => array('forest', array( 'lumber' => 6, 'fish' => 2 )), 
		  	'6' => array('water', ''), 
		  	'7' => array('water', ''), 
		  	'8' => array('water', ''), 
		  	'9' => array('water', ''), 
		  	'10' => array('water', ''), 
		),
	'8'  => array(
		  	'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('water', ''), 
		  	'4' => array('grass', array( 'sheep' => 8, 'arable_land' => 5, 'fish' => 1 )), 
		  	'5' => array('forest', array( 'lumber' => 9, 'sheep' => 1 )), 
		  	'6' => array('grass', array( 'sheep' => 7, 'arable_land' => 6, 'fish' => 2 )), 
		  	'7' => array('water', ''), 
		  	'8' => array('water', ''), 
		  	'9' => array('water', ''), 
		  	'10' => array('water', ''), 
		),
	'9'  => array(
			'1' => array('water', ''), 
		  	'2' => array('water', ''), 
		  	'3' => array('water', ''), 
		  	'4' => array('grass', array( )), 
		  	'5' => array('grass', array( )), 
		  	'6' => array('grass', array( )), 
		  	'7' => array('water', ''), 
		  	'8' => array('water', ''), 
		  	'9' => array('sand', array( )), 
		  	'10' => array('water', ''), 
		),
	'10' => array(
		  	'1' => array('water', ''), 
		  	'2' => array('sand', array( 'fish' => 9 )), 
		  	'3' => array('sand', array( 'fish' => 5, 'oil' => 3 )), 
		  	'4' => array('grass', array( 'arable_land' => 7 )), 
		  	'5' => array('grass', array( 'arable_land' => 6, 'stone' => 1 )), 
		  	'6' => array('hills', array( 'iron' => 5, 'stone' => 2 )), 
		  	'7' => array('grass', array( 'fish' => 3, 'arable_land' => 1 )), 
		  	'8' => array('grass', array( 'sheep' => 6 )), 
		  	'9' => array('hills', array( 'sheep' => 4, 'coal' => 2 )), 
		  	'10' => array('water', ''), 
		),
);
	$ID = get_page_by_title('Secondo 1', OBJECT, 'region');
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
	update_post_meta($ID, 'POS-y', '1');
?>

</head>
<body>
	<p>Secondo 1 updated.</p>
	<a href="<?= home_url(); ?>?region=secondo-1">Back to Secondo 1</a>
</body>
</html>