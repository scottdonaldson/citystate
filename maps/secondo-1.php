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
	1 => array(
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		),
	2 => array(
			array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		),
	3 => array(
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('sand', array( 'fish' => 8 )), 
		  	array('sand', array( 'fish' => 7, 'arable land' => 1 )),
		  	array('water', ''),
		),
	4  => array(
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('grass', array( 'fish' => 7, 'arable land' => 5 )), 
		  	array('grass', array( 'sheep' => 6, 'arable land' => 5, 'fish' => 4 )), 
		  	array('grass', array( 'sheep' => 8, 'arable land' => 5 )), 
		  	array('forest', array( 'lumber' => 9, 'sheep' => 2)), 
		),
	5  => array(
			array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('grass', array( 'arable land' => 6, 'fish' => 6, 'sheep' => 2 )), 
		  	array('forest', array( 'fish' => 7, 'lumber' => 5 )), 
		),
	6  => array(
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('grass', array( 'fish' => 9, 'sheep' => 5 )), 
		  	array('sand', array( 'fish' => 9 )), 
		  	array('water', ''), 
		  	array('water', ''), 
		    array('water', ''), 
		  	array('grass', array( 'fish' => 6, 'sheep' => 3 )), 	
		),
	7  => array(
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('sand', array( 'fish' => 8, 'arable land' => 1 )), 
		  	array('grass', array( 'fish' => 5, 'sheep' => 5, 'arable land' => 3 )), 
		  	array('forest', array( 'lumber' => 6, 'fish' => 2 )), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		),
	8  => array(
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('grass', array( 'sheep' => 8, 'arable land' => 5, 'fish' => 1 )), 
		  	array('forest', array( 'lumber' => 9, 'sheep' => 1 )), 
		  	array('grass', array( 'sheep' => 7, 'arable land' => 6, 'fish' => 2 )), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		),
	9  => array(
			array('water', ''), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('grass', array( )), 
		  	array('grass', array( )), 
		  	array('grass', array( )), 
		  	array('water', ''), 
		  	array('water', ''), 
		  	array('sand', array( )), 
		  	array('water', ''), 
		),
	10 => array(
		  	array('water', ''), 
		  	array('sand', array( 'fish' => 9 )), 
		  	array('sand', array( 'fish' => 5, 'oil' => 3 )), 
		  	array('grass', array( 'arable land' => 7 )), 
		  	array('grass', array( 'arable land' => 6, 'stone' => 1 )), 
		  	array('hills', array( 'iron' => 5, 'stone' => 2 )), 
		  	array('grass', array( 'fish' => 3, 'arable land' => 1 )), 
		  	array('grass', array( 'sheep' => 6 )), 
		  	array('hills', array( 'sheep' => 4, 'coal' => 2 )), 
		  	array('water', ''), 
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