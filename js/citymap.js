// If we're on the main map, we def
// want an oceanic background (maybe put this somewhere else?)
document.body.classList.add('ocean');
	// We will also want to show the city's neighbors...
	// Maybe even complete with structures?!

CS.returnStructures = function() {
	var output = '';
	output += '<select onchange="CS.showBuildStructure(this.options[this.selectedIndex].value);">';
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
	var output = '';
	output += '<label>Build a structure?</label><br>' + 
			  CS.returnStructures() +
			  '<button id="build-structure-submit" name="build-structure-submit" onclick="buildNewStructure(this.parentNode);" style="display: none;">Build</button>';
	return output;
}

// Add a new structure to the city
CS.buildNewStructure = function(infobox) {
	// Find the selected structure
	// Push it to the structures array of this city with level 0
	for (var i = 0; i < infobox.children.length; i++) {
		if (infobox.children[i].tagName === 'SELECT') {
			// Add to the structures array in this city
			CS.DATA.child('cities').child(CS.SLUG).child('structures').push({
				level: 0,
				name: infobox.children[i].value,
				x: X,
				y: Y
			});
			break;
		}
	}
	// Subtract cash from user
	CS.DATA.child('users').child(CS.USER).update({ 
		cash: (+localStorage.getItem('USER.cash')) - CS.STRUCTURES[infobox.children[i].value].cost
	});

	CS.hideInfobox();
}


CS.showStructure = function(x, y) {

}

CS.showCityTiles = function(data, cityUser, map) {
	
	// Create an empty array that we will fill with structures
	// after they've been shown, using Firebase's unique key for them
	var shownStructures = [];

	for (var x = 0; x < 10; x++) {
		for (var y = 0; y < 10; y++) {
			var tile = map.rect(CS.TILE_WIDTH * x, CS.TILE_WIDTH * y, CS.TILE_WIDTH, CS.TILE_WIDTH)
				.attr({ 
					'class': 'tile sand'
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
		// Set var for the user of this city (will check against current user)
		var cityUser = data.child('cities').child(CS.SLUG).child('user').val();

		// Update user cash in localStorage
		if (!!CS.USER) {
			localStorage.setItem('USER.cash', data.child('users').child(CS.USER).child('cash').val());
		}

		// Show the city tiles
		CS.showCityTiles(data.child('cities').val()[CS.SLUG], cityUser, Snap('#map'));
	});
}