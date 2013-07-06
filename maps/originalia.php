<?php
/*
Template Name: Originalia bulk upload
*/
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Originalia bulk upload</title>

<?php

	$originalia = array(
		'1' => array(
			  	'1' => array('water', ''), 
			  	'2' => array('sand', array( 'fish' => 9, 'coal' => 2, 'arable_land' => 2 )),
			  	'3' => array('hills', array( 'coal' => 8, 'iron' => 5, 'fish' => 4, 'arable_land' => 1 )),
			  	'4' => array('mountains', array( 'iron' => 6, 'coal' => 4, 'uranium' => 4, 'fish' => 3 )),
			  	'5' => array('hills', array( 'coal' => 5, 'iron' => 4, 'fish' => 4 )), 
			  	'6' => array('grass', array( 'fish' => 8, 'arable_land' => 7, 'sheep' => 3 )), 
			  	'7' => array('water', ''),
			  	'8' => array('water', ''),
			  	'9' => array('water', ''),
			  	'10' => array('water', ''),
			),
		'2' => array(
				'1' => array('water', ''),  
				'2' => array('water', ''),  
				'3' => array('sand', array( 'fish' => 3 )), 
				'4' => array('mountains', array( 'uranium' => 7, 'iron' => 5, 'stone' => 3 )), 
				'5' => array('mountains', array( 'iron' => 8, 'stone' => 6, 'coal' => 4, 'gold' => 2 )), 
				'6' => array('hills', array( 'coal' => 10, 'arable_land' => 2, 'sheep' => 2 )), 
				'7' => array('grass', array( 'arable_land' => 7, 'sheep' => 5, 'fish' => 4 )), 
				'8' => array('sand', array( 'oil' => 5, 'fish' => 5)), 
				'9' => array('water', ''),  
				'10' => array('water', ''), 
			),
		'3' => array(
				'1' => array('sand', array( 'fish' => 6 )), 
				'2' => array('sand', array( 'oil' => 3, 'fish' => 3 )), 
				'3' => array('hills', array( 'coal' => 7, 'arable_land' => 5 )), 
				'4' => array('mountains', array( 'iron' => 4, 'stone' => 3, 'gold' => 1 )), 
				'5' => array('hills', array( 'coal' => 5, 'arable_land' => 3, 'lumber' => 1 )), 
				'6' => array('hills', array( 'coal' => 5, 'lumber' => 5, 'iron' => 2 )), 
				'7' => array('hills', array( 'coal' => 7, 'lumber' => 2, 'arable_land' => 2 )), 
				'8' => array('grass', array( 'arable_land' => 8, 'sheep' => 5, 'fish' => 5 )), 
				'9' => array('water', ''), 
				'10' => array('water', ''),
			),
		'4'  => array(
				'1' => array('grass', array( 'fish' => 10, 'arable_land' => 6 )), 
				'2' => array('grass', array( 'arable_land' => 6, 'lumber' => 3, 'fish' => 2 )), 
				'3' => array('mountains', array( 'iron' => 8, 'uranium' => 3 )), 
				'4' => array('hills', array( 'coal' => 7, 'iron' => 5 )), 
				'5' => array('hills', array( 'iron' => 3, 'lumber' => 2 )), 
				'6' => array('forest', array( 'lumber' => 9, 'arable_land' => 2 )), 
				'7' => array('forest', array( 'lumber' => 8, 'arable_land' => 6 )), 
				'8' => array('grass', array( 'arable_land' => 10, 'sheep' => 8, 'fish' => 1 )), 
				'9' => array('grass', array( 'arable_land' => 8, 'sheep' => 6, 'fish' => 4 )), 
				'10' => array('water', ''),
			),
		'5'  => array(
				'1' => array('water', ''), 
				'2' => array('water', ''), 
				'3' => array('water', ''), 
				'4' => array('sand', array( 'fish' => 8 )), 
				'5' => array('forest', array( 'lumber' => 8, 'sheep' => 2 )), 
				'6' => array('forest', array( 'lumber' => 10 )), 
				'7' => array('grass', array( 'sheep' => 8, 'arable_land' => 5, 'lumber' => 1 )), 
				'8' => array('grass', array( 'arable_land' => 7, 'sheep' => 3 )), 
				'9' => array('grass', array( 'arable_land' => 5, 'sheep' => 3 )), 
				'10' => array('sand', array( 'fish' => 7, 'sheep' => 1 )),
			),
		'6'  => array(
				'1' => array('water', ''), 
				'2' => array('water', ''), 
				'3' => array('water', ''), 
				'4' => array('water', ''), 
				'5' => array('grass', array( 'arable_land' => 5, 'lumber' => 4, 'sheep' => 4 )), 
				'6' => array('forest', array( 'lumber' => 9, 'sheep' => 2 )), 
				'7' => array('grass', array( 'lumber' => 4, 'arable_land' => 4 )), 
				'8' => array('hills', array( 'coal' => 6, 'iron' => 5 )), 
				'9' => array('grass', array( )), 
				'10' => array('sand', array( )),
			),
		'7'  => array(
				'1' => array('water', ''), 
				'2' => array('water', ''),
				'3' => array('grass', array( 'fish' => 10, 'arable_land' => 4 )), 
				'4' => array('sand', array( 'fish' => 3, 'oil' => 1 )), 
				'5' => array('grass', array( 'arable_land' => 6, 'fish' => 2 )), 
				'6' => array('grass', array( 'arable_land' => 8, 'sheep' => 3, 'lumber' => 2 )), 
				'7' => array('grass', array( 'arable_land' => 7, 'sheep' => 5 )), 
				'8' => array('grass', array( 'arable_land' => 6, 'coal' => 1, 'fish' => 1 )), 
				'9' => array('sand', array( 'fish' => 4 )), 
				'10' => array('grass', array( 'arable_land' => 2, 'sheep' => 2, 'fish' => 2 )),
			),
		'8'  => array(
				'1' => array('grass', array( 'sheep' => 8, 'arable_land' => 6, 'fish' => 6 )), 
				'2' => array('grass', array( 'fish' => 9, 'sheep' => 7, 'arable_land' => 3 )), 
				'3' => array('water', ''), 
				'4' => array('grass', array( 'fish' => 4, 'lumber' => 4, 'arable_land' => 3 )), 
				'5' => array('forest', array( 'lumber' => 9, 'sheep' => 2 )), 
				'6' => array('forest', array( 'lumber' => 7, 'arable_land' => 5 )), 
				'7' => array('grass', array( 'sheep' => 7, 'arable_land' => 5 )), 
				'8' => array('water', ''), 
				'9' => array('water', ''), 
				'10' => array('water', ''),
			),
		'9'  => array(
				'1' => array('sand', array( 'fish' => 6 )), 
				'2' => array('hills', array( 'iron' => 10 )), 
				'3' => array('water', ''), 
				'4' => array('water', ''), 
				'5' => array('grass', array( 'arable_land' => 5, 'fish' => 3)), 
				'6' => array('grass', array( 'sheep' => 6, 'arable_land' => 4 )), 
				'7' => array('sand', array( 'fish' => 2 )), 
				'8' => array('grass', array( 'arable_land' => 6, 'fish' => 2 )), 
				'9' => array('water', ''), 
				'10' => array('water', ''),
			),
		'10' => array(
				'1' => array('water', ''), 
				'2' => array('grass', array( 'fish' => 8, 'sheep' => 4 )), 
				'3' => array('sand', array( 'fish' => 6 )), 
				'4' => array('sand', array( 'fish' => 5 )), 
				'5' => array('sand', array( 'fish' => 1 )), 
				'6' => array('sand', array( 'fish' => 1 )), 
				'7' => array('sand', array( 'fish' => 2 )), 
				'8' => array('grass', array( 'arable_land' => 5, 'fish' => 3 )), 
				'9' => array('grass', array( 'arable_land' => 4, 'sheep' => 4, 'fish' => 3 )), 
				'10' => array('water', ''),
			),
	);
	$ID = get_page_by_title('Originalia', OBJECT, 'region');
	$ID = $ID->ID;
	foreach ($originalia as $y => $row) {
		foreach ($row as $x => $tile) {
			update_post_meta($ID, $x.','.$y.'-terrain', $tile[0]);

			foreach ($tile[1] as $resource => $value) {
				update_post_meta($ID, $x.','.$y.'-'.$resource, $value);
			}
		}
	}
	update_post_meta($ID, 'POS-x', '1');
	update_post_meta($ID, 'POS-y', '1');
?>

</head>
<body>
	<p>Originalia updated.</p>
	<a href="<?= home_url(); ?>?region=originalia">Back to Originalia</a>
</body>
</html>