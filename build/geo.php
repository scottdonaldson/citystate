<?php

$geo = array('nw', 'n', 'ne', 'w', 'e', 'sw', 's', 'se');
foreach ($geo as $cardinal) {
	// Get region map (might be resetting)
	include( MAIN .'maps/'.$region_slug.'.php');

	// Find $map_x and $map_y on the map and set value (land or water)
	switch ($cardinal) {
		case 'nw':
			// Are we in the upper-left corner?
			if ($x == 1 && $y == 1) {
				$rel = 'nw';
				include( MAIN .'maps/'.$neighbors[$rel].'.php');
				$map_x = 10; $map_y = 10;
			// In the leftmost column?
			} elseif ($x == 1) {
				$rel = 'w';
				include( MAIN .'maps/'.$neighbors[$rel].'.php');
				$map_x = 10; $map_y = $y - 1;
			// In the top row?
			} elseif ($y == 1) {
				$rel = 'n';
				include( MAIN .'maps/'.$neighbors[$rel].'.php');
				$map_x = $x - 1; $map_y = 10;
			} else {
				$map_x = $x - 1; $map_y = $y - 1;
			}
			break;
		case 'n':
			// Are we in the top row?
			if ($y == 1) {
				$rel = 'n';
				include( MAIN .'maps/'.$neighbors[$rel].'.php');
				$map_x = $x; $map_y = 10;
			} else {
				$map_x = $x; $map_y = $y - 1; 
			}
			break;
		case 'ne':
			// Are we in the upper-right corner?
			if ($x == 10 && $y == 1) {
				$rel = 'ne';
				include( MAIN .'maps/'.$neighbors[$rel].'.php');
				$map_x = 1; $map_y = 10;
			// In the rightmost column?	
			} elseif ($x == 10) {
				$rel = 'e';
				include( MAIN .'maps/'.$neighbors[$rel].'.php');
				$map_x = 1; $map_y = $y - 1;
			// In the top row?	
			} elseif ($y == 1) {
				$rel = 'n';
				include( MAIN .'maps/'.$neighbors[$rel].'.php');
				$map_x = $x + 1; $map_y = 10;
			} else {
				$map_x = $x + 1; $map_y = $y - 1;
			}
			break;
		case 'w':
			// Are we in the leftmost column?
			if ($x == 1) {
				$rel = 'w';
				include( MAIN .'maps/'.$neighbors[$rel].'.php');
				$map_x = 10; $map_y = $y;
			} else {
				$map_x = $x - 1; $map_y = $y;
			}
			break;
		case 'e':
			// Are we in the rightmost column?
			if ($x == 10) {
				$rel = 'e';
				include( MAIN .'maps/'.$neighbors[$rel].'.php');
				$map_x = 1; $map_y = $y;
			} else { 
				$map_x = $x + 1; $map_y = $y;
			}
			break;
		case 'sw':
			// Are we in the bottom left corner?
			if ($x == 1 && $y == 10) {
				$rel = 'sw';
				include( MAIN .'maps/'.$neighbors[$rel].'.php');
				$map_x = 10; $map_y = 1;
			// Leftmost column?
			} elseif ($x == 1) {
				$rel = 'w';
				include( MAIN .'maps/'.$neighbors[$rel].'.php');
				$map_x = 10; $map_y = $y + 1;
			// Bottom row?
			} elseif ($y == 10) {
				$rel = 's';
				include( MAIN .'maps/'.$neighbors[$rel].'.php');
				$map_x = $x - 1; $map_y = 1;
			} else {
				$map_x = $x - 1; $map_y = $y + 1;
			}
			break;
		case 's':
			// Are we in the bottom row?
			if ($y == 10) {
				$rel = 's';
				include( MAIN .'maps/'.$neighbors[$rel].'.php');
				$map_x = $x; $map_y = 1;
			} else {
				$map_x = $x; $map_y = $y + 1;	
			}
			break;
		case 'se':
			// Are we in the bottom right corner?
			if ($x == 10 && $y == 10) {
				$rel = 'se';
				include( MAIN .'maps/'.$neighbors[$rel].'.php');
				$map_x = 1; $map_y = 1;
			// In the right most column?
			} elseif ($x == 10) {
				$rel = 'e';
				include( MAIN .'maps/'.$neighbors[$rel].'.php');
				$map_x = 1; $map_y = $y + 1;
			// In the bottom row?
			} elseif ($y == 10) {
				$rel = 's';
				include( MAIN .'maps/'.$neighbors[$rel].'.php');
				$map_x = $x + 1; $map_y = 1;
			} else {
				$map_x = $x + 1; $map_y = $y + 1;	
			}
			break;	
	}	

	$val = $map[$map_y][$map_x - 1][0];
	if ($val == 'water') {
		$val = 'water';
	} else { 
		$val = 'land'; 
	}

	add_post_meta($ID, 'map-'.$cardinal, $val);
}

?>