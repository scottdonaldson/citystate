// If we're on the main map, we def
// want an oceanic background (maybe put this somewhere else?)
document.body.classList.add('ocean');

CS.cityCost = function() {
	CS.DATA.once('value', function(data){
		console.log(data.child('users').child(CS.USER).child('cities').val().length)
		return (data.child('users').child(CS.USER).child('cities').val().length);
	});
}

CS.buildCityForm = function(tile, terrain) {
	var output = '';
	var _cityCost = cityCost();
	var prep = terrain === 'forest' ? 'in' : 'on';
	var thisOrThese = terrain.slice(-1) === 's' && terrain !== 'grass' ? 'these' : 'this';
	terrain = terrain === 'grass' ? 'grassland' : terrain;
	output += '<label>Build a city ' + prep + ' ' + thisOrThese + ' uninhabited ' + terrain + '?</label>' + 
			  '<input type="text" name="cityName" placeholder="Name your city">' + 
			  '<input type="submit" onclick="CS.buildCity(tile, this.parentNode);" value="Build (' + _cityCost + ')">';

			  // TODO: submit the form... don't forget to add to the user's cities as well as top-level cities!
	return output;
}

CS.buildCity = function(tile, infobox) {
	console.log(tile);
	console.log(infobox);
}

// At the moment, structures in cities on the main map show up
// as a 2px x 2px square... eventually will make this
// more detailed (color, density, etc.)
CS.showStructure = function(city, structure, map) {
	return map.rect(
		CS.TILE_WIDTH * city.x + 4 * structure.x + 1, 
		CS.TILE_WIDTH * city.y + 4 * structure.y + 1,
		2,
		2)
		.attr({ fill: '#444' });
}

CS.showCityInfo = function(info) {
	return '<strong>' + info.data('city-name') + '</strong><br>Pop: ' + CS.commas(info.data('city-population'));
}

CS.goToCity = function(info) {
	location.assign( CS.BASE + '/city/#/' + info.data('city-slug') );
}

CS.showWorldMap = function() {
	
	// Snap the map,
	// and get ready for map tiles and cities
	var map = Snap('#map'),
		tiles,
		cities;
	
	// Once the data is ready...
	CS.DATA.once('value', function(data){

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
					var tile = map.rect( CS.TILE_WIDTH * x, CS.TILE_WIDTH * y, CS.TILE_WIDTH, CS.TILE_WIDTH)
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
								CS.showStructure(cities[city], cities[city].structures[structure], map);
							}
							tile.data('has-city', true);
							var facade = tile.clone().attr({'class': 'tile', 'fill': 'transparent'});

							facade.data('city-name', cities[city].name);
							facade.data('city-slug', city);
							facade.data('city-population', cities[city].population);

							facade.hover(function(e){
									CS.showInfobox(e, function(facade){
										CS.showCityInfo(facade);
									});
								}, function(){
									CS.hideInfobox();
								})
								.click(function(){
									CS.goToCity(this);
								});
							break;
						}
					}
					// If no city, click to prompt building one
					if (!tile.data('has-city')) {
						tile.click(function(e){
							CS.showInfobox(e, buildCityForm(tile, this.data('terrain')));
						});
					}
				}
			}
		}

		// Reset the shownCities array
		shownCities = [];
	});
}