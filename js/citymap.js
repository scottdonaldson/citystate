// If we're on the main map, we def
// want an oceanic background (maybe put this somewhere else?)
document.body.classList.add('ocean');
	// We will also want to show the city's neighbors...
	// Maybe even complete with structures?!

function returnStructures() {
	var output = '';
	output += '<select><option value=""></option>';
	for (var structure in STRUCTURES) {
		output += '<option value="' + structure + '">' + STRUCTURES[structure].name + '</option>';
	}
	output += '</select>';
	return output;
}

function buildStructureForm(tile) {

	var output = '';
	output += '<form>' + 
			  '<label>Build a structure?</label><br>' + 
			  returnStructures() +
			  // '<input type="submit" name="buildCity" value="Build">' + 
			  '</form>';
	return output;
}

function showStructure(x, y) {

}

function showCityTiles(data, cityUser, map) {
	
	// Create an empty array that we will fill with structures
	// after they've been shown, using Firebase's unique key for them
	var shownStructures = [];

	for (var x = 0; x < 10; x++) {
		for (var y = 0; y < 10; y++) {
			var tile = map.rect(TILE_WIDTH * x, TILE_WIDTH * y, TILE_WIDTH, TILE_WIDTH)
				.attr({ 
					'class': 'tile sand'
				});
				
				if (LOGGED_IN && 
					LOGGED_IN !== "false" &&
					+USER === cityUser) {

					tile.click(function(e){
						showInfobox(e, buildStructureForm(tile));
					});
				}

			// Is there a structure at this tile?
			(function(){
				for (var structure in data.structures) {
					if (shownStructures.indexOf(structure) === -1 && data.structures[structure].x === x && data.structures[structure].y === y) {

						shownStructures.push(structure);

						var s = data.structures[structure];
						return buildStructure(s.name, s.level, s.x, s.y, map);
					}
				}
			})();
		}
	}

	// Reset the shownStructures array
	shownStructures = [];
}

function showCityMap() {
	// Split the window URL by the hash and choose the last segment
	SLUG = window.location.href.split(BASE + '/city/#/');
	SLUG = SLUG[SLUG.length - 1];

	// Remove any modifiers that might have snuck in from the segment
	var modifiers = ['?', '&', '.'];
	for (var i = 0; i < modifiers.length; i++) {
		if (SLUG.indexOf(modifiers[i]) > -1) {
			SLUG = SLUG.slice(0, slug.indexOf(modifiers[i]));
		}
	}

	DATA.once('value', function(data){
		// Set var for the user of this city (will check against current user)
		var cityUser = data.child('cities').child(SLUG).child('user').val();

		// Update the global STRUCTURES object
		for (var structure in data.child('structures').val()){
			STRUCTURES[structure] = data.child('structures').val()[structure];
		}

		// Show the city tiles
		showCityTiles(data.child('cities').val()[SLUG], cityUser, Snap('#map'));
	});
}