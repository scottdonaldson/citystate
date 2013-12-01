// If we're on the main map, we def
// want an oceanic background (maybe put this somewhere else?)
document.body.classList.add('ocean');
	// We will also want to show the city's neighbors...
	// Maybe even complete with structures?!

function returnStructures() {
	var output = '';
	output += '<select onchange="showBuildStructure(this.options[this.selectedIndex].value);">';
	output += '<option value=""></option>';
	for (var structure in STRUCTURES) {
		// Only show the option if the user has enough cash
		// to build the structure
		if (+localStorage.getItem('USER.cash') >= STRUCTURES[structure].cost) {
			output += '<option value="' + structure + '">' + STRUCTURES[structure].name.charAt(0).toUpperCase() + STRUCTURES[structure].name.slice(1) + ' (' + STRUCTURES[structure].cost + ')</option>';
		}
	}
	output += '</select>';
	return output;
}

function showBuildStructure(structure) {
	if (structure) {
		document.getElementById('build-structure-submit').innerHTML = 'Build (' + STRUCTURES[structure].cost + ')';
		document.getElementById('build-structure-submit').style.display = 'block';
	} else {
		document.getElementById('build-structure-submit').style.display = 'none';
	}
}

function buildStructureForm(e, tile) {
	var output = '';
	output += '<label>Build a structure?</label><br>' + 
			  returnStructures() +
			  '<button id="build-structure-submit" name="build-structure-submit" onclick="buildNewStructure(this.parentNode);" style="display: none;">Build</button>';
	return output;
}

// Add a new structure to the city
function buildNewStructure(infobox) {
	// Find the selected structure
	// Push it to the structures array of this city with level 0
	for (var i = 0; i < infobox.children.length; i++) {
		if (infobox.children[i].tagName === 'SELECT') {
			// Add to the structures array in this city
			DATA.child('cities').child(SLUG).child('structures').push({
				level: 0,
				name: infobox.children[i].value,
				x: X,
				y: Y
			});
			break;
		}
	}
	// Subtract cash from user
	console.log((+localStorage.getItem('USER.cash')) - STRUCTURES[infobox.children[i].value].cost);
	DATA.child('users').child(USER).update({ 
		cash: (+localStorage.getItem('USER.cash')) - STRUCTURES[infobox.children[i].value].cost
	});

	hideInfobox();
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
						showInfobox(e, buildStructureForm(e, tile));
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
		// Update the global STRUCTURES object
		for (var structure in data.child('structures').val()){
			STRUCTURES[structure] = data.child('structures').val()[structure];
		}
	});
	DATA.on('value', function(data){
		// Set var for the user of this city (will check against current user)
		var cityUser = data.child('cities').child(SLUG).child('user').val();

		// Update user cash in localStorage
		localStorage.setItem('USER.cash', data.child('users').child(USER).child('cash').val());

		// Show the city tiles
		showCityTiles(data.child('cities').val()[SLUG], cityUser, Snap('#map'));
	});
}