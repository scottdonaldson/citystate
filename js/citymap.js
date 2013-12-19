// If we're on the main map, we def
// want an oceanic background (maybe put this somewhere else?)
document.body.classList.add('ocean');

CS.returnStructures = function() {
	var output = '';
	output += '<select id="structures-list" onchange="CS.showBuildStructure(this.options[this.selectedIndex].value);">';
	output += '<option value=""></option>';
	for (var structure in CS.STRUCTURES) {
		// Only show the option if the user has enough cash
		// to build the structure
		if (+localStorage.getItem('USER.cash') >= CS.STRUCTURES[structure].cost) {
			output += '<option value="' + structure + '">' + CS.STRUCTURES[structure].name.charAt(0).toUpperCase() + CS.STRUCTURES[structure].name.slice(1) + ' (' + CS.STRUCTURES[structure].cost + ')</option>';
		}
	}
	output += '</select>';
	return output;
}

CS.showBuildStructure = function(structure) {
	if (structure) {
		CS('#build-structure-submit').innerHTML = 'Build (' + CS.STRUCTURES[structure].cost + ')';
		CS('#build-structure-submit').style.display = 'block';
	} else {
		CS('#build-structure-submit').style.display = 'none';
	}
}

CS.buildStructureForm = function(e, tile) {
	var output = document.createElement('form');
	output.innerHTML = 
		'<label>Build a structure?</label><br>' + 
		CS.returnStructures() +
		'<button id="build-structure-submit" name="build-structure-submit" style="display: none;">Build</button>';

	output.addEventListener('submit', function(e){
		e.preventDefault();
		// If a value has been selected, build the new structure
		if ( CS('#structures-list').value ) {
			CS.buildNewStructure();
		}
	});

	return output;
}

// Add a new structure to the city
CS.buildNewStructure = function(infobox) {
	// Find the selected structure
	// Push it to the structures array of this city with level 0

	// Add to the structures array in this city
	CS.DATA.child('cities').child(CS.SLUG).child('structures').push({
		level: 0,
		name: CS('#structures-list').value,
		x: X,
		y: Y
	});

	// Subtract cash from user
	CS.DATA.child('users').child(CS.USER).update({ 
		cash: ( +localStorage.getItem('USER.cash') ) - CS.STRUCTURES[CS('#structures-list').value].cost
	});

	CS.hideInfobox();
}

CS.showCityTiles = function(data, cityUser, terrain, map) {
	
	// Create an empty array that we will fill with structures
	// after they've been shown, using Firebase's unique key for them
	var shownStructures = [];

	for (var x = 0; x < 10; x++) {
		for (var y = 0; y < 10; y++) {
			var tile = map.rect(CS.TILE_WIDTH * x, CS.TILE_WIDTH * y, CS.TILE_WIDTH, CS.TILE_WIDTH)
				.attr({ 
					'class': 'tile ' + terrain
				});
		
				// Only allow the logged in builder of this city
				// to build structures		
				if (CS.LOGGED_IN && 
					CS.LOGGED_IN !== "false" &&
					+CS.USER === cityUser) {

					tile.click(function(e){
						CS.showInfobox(e, CS.buildStructureForm(e, tile));
					});
				}

			// Is there a structure at this tile?
			(function(){
				for (var structure in data.structures) {
					if (shownStructures.indexOf(structure) === -1 && data.structures[structure].x === x && data.structures[structure].y === y) {

						shownStructures.push(structure);

						var s = data.structures[structure];
						return CS.buildStructure(s.name, s.level, s.x, s.y, map);
					}
				}
			})();
		}
	}

	// Reset the shownStructures array
	shownStructures = [];
}

CS.showCityMap = function() {
	// Split the window URL by the hash and choose the last segment
	CS.SLUG = CS.parseSlug('city');

	CS.DATA.on('value', function(data){

		// Get a reference to this city
		var cityRef = data.child('cities').child(CS.SLUG);

		// Set var for the user of this city (will check against current user),
		// and for the x and y coordinates of the city
		var cityUser = cityRef.child('user').val(),
			cityX = cityRef.child('x').val(),
			cityY = cityRef.child('y').val();

		// Set terrain of the city
		var terrain = data.child('regions').child('argyle-island').child('tiles').child(cityX).child(cityY).val();

		// Update user cash in localStorage (in case it has changed)
		if ( CS.LOGGED_IN ) {
			localStorage.setItem('USER.cash', data.child('users').child(CS.USER).child('cash').val());
		}

		// Show the city tiles
		CS.showCityTiles(data.child('cities').child(CS.SLUG).val(), cityUser, terrain, Snap('#map'));

		// Show the neighbor cities
		CS.showNeighbors(data);
	});
}

CS.showNeighbors = function(data) {
	// Object for cardinal directions relative to map
	var cardinals = {
		nww: [-2, -1],
		nw:  [-1, -1],
		n:   [ 0, -1],
		ne:  [ 1, -1],
		nee: [ 2, -1],
		ww:  [-2,  0],
		w:   [-1,  0],
		e:   [ 1,  0],
		ee:  [ 2,  0],
		sww: [-2,  1],
		sw:  [-1,  1],
		s:   [ 0,  1],
		se:  [ 1,  1],
		see: [ 2,  1]
	}
	// Get a reference to this city in order to find the neighbor in the region
	var cityRef = data.child('cities').child(CS.SLUG),
		cityX = cityRef.child('x').val(),
		cityY = cityRef.child('y').val();

	var shownCities = [];
	for (var cardinal in cardinals) {
		
		var neighbor = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
		neighbor.id = cardinal;
		neighbor.classList.add('map', 'neighbor');
		CS('#main').appendChild( neighbor );
		
		// Find terrain from the region tiles (or, if not present, call it water)
		var terrain = data.child('regions').child('argyle-island').child('tiles').child(cityX + cardinals[cardinal][0]).child(cityY + cardinals[cardinal][1]).val() || 'water';
		var neighborData = {};

		// Find the city at this neighbor if there is one
		for (var city in data.child('cities').val()) {
			var newCityRef = data.child('cities').child(city).val();

			if (newCityRef.x === cityX + cardinals[cardinal][0] &&
				newCityRef.y === cityY + cardinals[cardinal][1] &&
				shownCities.indexOf( newCityRef.slug ) === -1) {

				shownCities.push( newCityRef.slug );
				neighborData = newCityRef;
				break;
			}
		}
		
		CS.showCityTiles(neighborData, false, terrain, Snap('#' + cardinal));
	}
	shownCities = [];
}