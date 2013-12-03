// If we're on the main map, we def
// want an oceanic background (maybe put this somewhere else?)
document.body.classList.add('ocean');

function buildCityForm(terrain) {
	var output = '';
	var prep = terrain === 'forest' ? 'in' : 'on';
	var thisOrThese = terrain.slice(-1) === 's' && terrain !== 'grass' ? 'these' : 'this';
	terrain = terrain === 'grass' ? 'grassland' : terrain;
	output += '<form action="?build=true" method="POST">' + 
			  '<label>Build a city ' + prep + ' ' + thisOrThese + ' uninhabited ' + terrain + '?</label>' + 
			  '<input type="text" name="cityName" placeholder="Name your city">' + 
			  '<input type="submit" name="buildCity" value="Build">' + 
			  '</form>';

			  // TODO: submit the form... don't forget to add to the user's cities as well as top-level cities!
	return output;
}

// At the moment, structures in cities on the main map show up
// as a 2px x 2px square... eventually will make this
// more detailed (color, density, etc.)
function showStructure(city, structure, map) {
	return map.rect(
		TILE_WIDTH * city.x + 4 * structure.x + 1, 
		TILE_WIDTH * city.y + 4 * structure.y + 1,
		2,
		2)
		.attr({ fill: '#444' });
}

function showCityInfo(info) {
	return '<strong>' + info.data('city-name') + '</strong><br>Pop: ' + commas(info.data('city-population'));
}

function goToCity(info) {
	window.location = BASE + '/city/#/' + info.data('city-slug');
}

function showWorldMap() {
	
	// Snap the map,
	// and get ready for map tiles and cities
	var map = Snap('#map'),
		tiles,
		cities;
	
	// Once the data is ready...
	DATA.once('value', function(data){

		// Create an empty array that we will fill with cities (unique by slug)
		// after they've been shown
		var shownCities = [];

		// Set tiles and cities.
		// FOR NOW, we are only using the 'argyle-island' region...
		// eventually will expand
		tiles = data.child('regions').child('argyle-island').child('tiles').val();
		cities = data.child('cities').val();
	
		// Loop through the tiles' x and y values
		for (var x = 0; x < tiles.length; x++) {
			for (var y = 0; y < tiles[x].length; y++) {
				if (tiles[x][y] !== 'water') {
					var tile = map.rect( TILE_WIDTH * x, TILE_WIDTH * y, TILE_WIDTH, TILE_WIDTH)
						.attr({ 
							'class': 'tile ' + tiles[x][y]
						})
						.data('terrain', tiles[x][y])
						.data('has-city', false);

					// Check to see if there's a city at this tile
					for (var city in cities) {

						// If the city has not been shown, 
						// and it is at this tile location,
						// add it to the shownCities array
						if (shownCities.indexOf(city) === -1 && 
							cities[city].x === x && 
							cities[city].y === y) {

							shownCities.push(city);

							for (var structure in cities[city].structures) {
								showStructure(cities[city], cities[city].structures[structure], map);
							}
							tile.data('has-city', true);
							var facade = tile.clone().attr({'class': 'tile', 'fill': 'transparent'});

							facade.data('city-name', cities[city].name);
							facade.data('city-slug', city);
							facade.data('city-population', cities[city].population);

							facade.hover(function(e){
									showInfobox(e, showCityInfo(this));
								}, hideInfobox)
								.click(function(){
									goToCity(this);
								});
							break;
						}
					}
					// If no city, click to prompt building one
					if (!tile.data('has-city')) {
						tile.click(function(e){
							showInfobox(e, buildCityForm(this.data('terrain')));
						});
					}
				}
			}
		}

		// Reset the shownCities array
		shownCities = [];
	});
}