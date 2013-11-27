var tileWidth = 40;

function setTransform(d) {
	return new Snap.Matrix().translate(tileWidth * (d.x - 1) + d.translateX, tileWidth * (d.y - 1) + d.translateY);
}

/* ----- Landscape ----- */

function buildTree(map, d) {
	var _trunk = map.path('M0,0 h4 v-10 h-4 Z').attr({ fill: '#220' }),
		_bigLeaves = map.circle(2, -20, 10).attr({ fill: '#095' }),
		_leaf1 = map.circle(6, -16, 2).attr({ fill: '#7d6' }),
		_leaf2 = map.circle(-4, -20, 2).attr({ fill: '#7d6' }),
		_leaf3 = map.circle(4, -25, 2).attr({ fill: '#7d6' });
	return map.g(_trunk, _bigLeaves, _leaf1, _leaf2, _leaf3).transform(setTransform(d));
}

/* ----- Parts of buildings ----- */

function buildDoor(map, x, y) {
	return map.rect(x, y, 4, 8);
}

function buildWindow(map, x, y) {
	return map.rect(x, y, 4, 4);
}

/* ----- Buildings ----- */

function buildHouse(map, d) {
	// Template for a single house
	var _house = map.rect(0, 0, 20, 10).attr({ fill: d.fill }),
		_door = buildDoor(map, 3, 2),
		_roof = map.path('M-2,0 h24 l-12,-6 l-12,6 Z').attr({ fill: '#000' }),
		_window = buildWindow(map, 12, 2);
	return map.g(_house, _door, _roof, _window).transform(setTransform(d));
}

function buildLibrary(map, d) {
	var _groundfloor = map.rect(0, -10, 30, 10).attr({ fill: d.fill }),
		_secondfloor = map.rect(0, -20, 20, 10).attr({ fill: d.fill }),
		_door = buildDoor(map, 3, -8),
		_roof1 = map.rect(20, -13, 11, 3).attr({ fill: '#000' }),
		_roof2 = map.rect(-1, -23, 22, 3).attr({ fill: '#000' }),
		_windows = map.g(
			map.rect(3, -18, 4, 4),
			map.rect(13, -18, 4, 4),
			map.rect(13, -8, 4, 4),
			map.rect(23, -8, 4, 4)
			);
	return map.g(_groundfloor, _secondfloor, _door, _roof1, _roof2, _windows).transform(setTransform(d));
}

function buildStructure(which, level, x, y, map) {
	// Defaults for each
	var d = {
		x: x,
		y: y,
		fill: '#000',
		level: level,
		translateX: 0,
		translateY: 0
	};
	switch (which) {
		case 'neighborhood':
			if (level <= 1) {
				d.translateX = 4;
				d.translateY = 8;
				d.fill = '#999';
				buildHouse(map, d);
				
				// At level 1, introduce another house
				if (level === 1) {
					d.translateX = 10;
					d.translateY = 26;
					buildHouse(map, d);
				}

				d.translateX = 28;
				d.translateY = 40;
				buildTree(map, d);
			} else if (level === 2) {
				// TODO
			}
			break;

		case 'library':
			d.translateX = 28;
			d.translateY = 35;
			buildTree(map, d);

			d.translateX = 5;
			d.translateY = 40;
			d.fill = '#966';
			buildLibrary(map, d);
			break;
	}
}