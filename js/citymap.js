// If we're on the main map, we def
// want an oceanic background (maybe put this somewhere else?)
document.body.classList.add('ocean');
	// We will also want to show the city's neighbors...
	// Maybe even complete with structures?!

function buildStructureForm() {
	return 'asdf';
}

function showStructure(x, y) {

}

function showCityTiles(data, map) {
	
	// Create an empty array that we will fill with structures
	// after they've been shown, using Firebase's unique key for them
	var shownStructures = [];

	for (var x = 0; x < 10; x++) {
		for (var y = 0; y < 10; y++) {
			var tile = map.rect(TILE_WIDTH * x, TILE_WIDTH * y, TILE_WIDTH, TILE_WIDTH)
				.attr({ 
					'class': 'tile sand'
				})
				.click(function(e){
					showInfobox(e, buildStructureForm());
				});

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
	var slug = window.location.href.split(BASE + '/city/#/');
		slug = slug[slug.length - 1];

	// Remove any modifiers that might have snuck in from the segment
	var modifiers = ['?', '&', '.'];
	for (var i = 0; i < modifiers.length; i++) {
		if (slug.indexOf(modifiers[i]) > -1) {
			slug = slug.slice(0, slug.indexOf(modifiers[i]));
		}
	}

	DATA.once('value', function(data){
		showCityTiles(data.child('cities').val()[slug], Snap('#map'));
	});
}