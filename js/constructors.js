CS.setTransform = function(d) {
	return new Snap.Matrix().translate(CS.TILE_WIDTH * d.x + d.translateX, CS.TILE_WIDTH * d.y + d.translateY);
}

/* ----- Landscape ----- */

CS.buildTree = function(map, d) {
	var _trunk = map.path('M0,0 h4 v-10 h-4 Z').attr({ fill: '#220' }),
		_bigLeaves = map.circle(2, -20, 10).attr({ fill: '#095' }),
		_leaf1 = map.circle(6, -16, 2).attr({ fill: '#7d6' }),
		_leaf2 = map.circle(-4, -20, 2).attr({ fill: '#7d6' }),
		_leaf3 = map.circle(4, -25, 2).attr({ fill: '#7d6' });
	return map.g(_trunk, _bigLeaves, _leaf1, _leaf2, _leaf3).transform(CS.setTransform(d));
}

/* ----- Parts of buildings ----- */

CS.buildDoor = function(map, x, y) {
	return map.rect(x, y, 4, 8);
}

CS.buildWindow = function(map, x, y) {
	return map.rect(x, y, 4, 4);
}

/* ----- Buildings ----- */

CS.buildHouse = function(map, d) {
	// Template for a single house
	var _house = map.rect(0, 0, 20, 10).attr({ fill: d.fill }),
		_door = CS.buildDoor(map, 3, 2).attr({ fill: '#522' }),
		_roof = map.path('M-2,0 h24 l-12,-6 l-12,6 Z').attr({ fill: '#522' }),
		_window = CS.buildWindow(map, 12, 2).attr({ fill: '#522' });
	return map.g(_house, _door, _roof, _window).transform(CS.setTransform(d));
}

CS.buildLibrary = function(map, d) {
	var _groundfloor = map.rect(0, -10, 30, 10).attr({ fill: d.fill }),
		_secondfloor = map.rect(0, -20, 20, 10).attr({ fill: d.fill }),
		_door = CS.buildDoor(map, 3, -8),
		_roof1 = map.rect(20, -13, 11, 3).attr({ fill: '#000' }),
		_roof2 = map.rect(-1, -23, 22, 3).attr({ fill: '#000' }),
		_windows = map.g(
			map.rect(3, -18, 4, 4),
			map.rect(13, -18, 4, 4),
			map.rect(13, -8, 4, 4),
			map.rect(23, -8, 4, 4)
		);
	return map.g(_groundfloor, _secondfloor, _door, _roof1, _roof2, _windows).transform(CS.setTransform(d));
}

CS.buildStadium = function(map, d) {
	var _plan = map.rect(0, -70, 70, 70, 10, 10).attr({ fill: '#757' }),
		_field = map.rect(15, -55, 50, 50, 5, 5).attr({ fill: '#8c7' });
	return map.g(_plan, _field).transform(CS.setTransform(d));
}

/* ----- Master buildStructure() function ----- */

CS.buildStructure = function(which, level, x, y, map) {
	// Defaults for each
	var d = {
		x: x,
		y: y,
		fill: '#000',
		level: level,
		sizeX: CS.STRUCTURES[which].x,
		sizeY: CS.STRUCTURES[which].y,
		translateX: 0,
		translateY: 0
	},
	parts = [];
	switch (which) {
		case 'park':
			d.translateX = 8;
			d.translateY = 30;
			parts.push( CS.buildTree(map, d) );

			d.translateX = 26;
			d.translateY = 30;
			parts.push( CS.buildTree(map, d) );

			if (level === 1) {
				d.translateX = 17;
				d.translateY = 40;
				parts.push( CS.buildTree(map, d) );
			}

			break;

		case 'neighborhood':
			if (level <= 1) {
				d.translateX = 4;
				d.translateY = 8;
				d.fill = '#f9f2f2';
				parts.push( CS.buildHouse(map, d) );
				
				// At level 1, introduce another house
				if (level === 1) {
					d.translateX = 10;
					d.translateY = 26;
					parts.push( CS.buildHouse(map, d) );
				}

				d.translateX = 28;
				d.translateY = 40;
				parts.push( CS.buildTree(map, d) );
			} else if (level === 2) {
				// TODO
			}
			break;

		case 'library':
			d.translateX = 28;
			d.translateY = 35;
			parts.push( CS.buildTree(map, d) );

			d.translateX = 5;
			d.translateY = 40;
			d.fill = '#966';
			parts.push( CS.buildLibrary(map, d) );
			break;

		case 'stadium':
			d.translateX = 5;
			d.translateY = 80;
			parts.push( CS.buildStadium(map, d) );
			break;
	}

	var facade = map.rect( CS.TILE_WIDTH * x, CS.TILE_WIDTH * y, CS.TILE_WIDTH * d.sizeX, CS.TILE_WIDTH * d.sizeY ).attr({
		fill: 'transparent'
	});
	parts.push(facade);
	return map.g.apply(map, parts);
}