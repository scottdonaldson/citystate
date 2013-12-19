// If we're on the main map, we def
// want an oceanic background (maybe put this somewhere else?)
document.body.classList.add('ocean');

// Return the cost to build a city, based on number of
// cities the user already has
CS.cityCost = function() {
	return 1500 * ( +localStorage.getItem('USER.cities') ) + 500;
}

CS.buildCityForm = function(tile, terrain) {

	// See if user has enough cash, output either a form or div depending on that
	var hasEnoughCash = ( +localStorage.getItem('USER.cash') ) >= CS.cityCost(),
		output = hasEnoughCash ? document.createElement('form') : document.createElement('div');

	// Language helpers
	var prep = terrain === 'forest' ? 'in' : 'on',
		thisOrThese = terrain.slice(-1) === 's' && terrain !== 'grass' ? 'these' : 'this';
		terrain = terrain === 'grass' ? 'grassland' : terrain;

	output.innerHTML += hasEnoughCash ? 

		'<label>Build a city ' + prep + ' ' + thisOrThese + ' uninhabited ' + terrain + '?</label>' + 
		'<input type="text" id="city-name" placeholder="Name your city">' +
		'<input type="submit" value="Build (' + CS.commas( CS.cityCost() ) + ')">' : 

		'<p>Not enough funds to build a new city!</p>';

	// If outputting a form, make sure to build a city when the user submits it
	if (output.tagName === 'FORM') {
		output.addEventListener( 'submit', function(e){
			e.preventDefault();
			CS.buildCity();
		});
	}
	return output;
}

CS.buildCity = function() {

	// Subtract cash from user
	CS.DATA.child('users').child(CS.USER).update({ 
		cash: ( +localStorage.getItem('USER.cash') ) - CS.cityCost()
	});

	// Slugify the city name
	var name = CS('#city-name').value,
		slug = CS.slugify( name );

	CS.DATA.once('value', function(data){
		// If there's already a city with this slug, add a '-1' to the end of it
		if ( data.child('cities').child(slug).val() ) {
			slug += '-1';
		}
		// Push to the (global) cities array
		CS.DATA.child('cities').child(slug).set({
			name: name,
			population: 0,
			'target-pop': 0,
			user: +CS.USER,
			x: X,
			y: Y
		});
		// Add the unique slug to the user's cities array
		var key = data.child('users').child(CS.USER).child('cities').val() ? data.child('users').child(CS.USER).child('cities').val().length : 0;
		CS.DATA.child('users').child(CS.USER).child('cities').child(key).set(slug);

		// Redirect to the new city
		location.assign( CS.BASE + '/city/#/' + slug);
	});
	CS.hideInfobox();

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
	var output = document.createElement('div');
	output.innerHTML = '<strong>' + info.data('city-name') + '</strong>' +
					   '<br>Pop: ' + CS.commas(info.data('city-population'));
	return output;
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

		// Set the number of cities the user currently has (if they have any cities!)
		if ( CS.LOGGED_IN && data.child('users').child(CS.USER).child('cities').val() ) {
			localStorage.setItem( 'USER.cities', data.child('users').child(CS.USER).child('cities').val().length );
		
		// If logged in but no cities yet, set that too
		} else if ( CS.LOGGED_IN && !data.child('users').child(CS.USER).child('cities').val() ) {
			localStorage.setItem( 'USER.cities', 0 );
		}

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
					var tile = map.rect( CS.TILE_WIDTH * x, CS.TILE_WIDTH * y, CS.TILE_WIDTH, CS.TILE_WIDTH )
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
							break;
						}
					}
					// If no city, click to prompt building one
					if (!tile.data('has-city')) {
						// Only, that is, if the user is logged in
						if ( CS.LOGGED_IN ) {
							tile.click(function(e){
								CS.showInfobox(e, CS.buildCityForm(tile, this.data('terrain')));
							});
						}

					// Otherwise, we'll create a facade (to cover structures)
					// and hover and click for city-specific stuff
					} else {
						var facade = tile.clone().attr({ 'class': 'tile facade', 'fill': 'transparent' });

						facade.data('city-name', cities[city].name);
						facade.data('city-slug', city);
						facade.data('city-population', cities[city].population);

						// Put the facade as last element in the map so there aren't weird hover effects
						map.append(facade);

						facade.hover(function(e){
								CS.showInfobox(e, CS.showCityInfo(this));
							}, function(){
								CS.hideInfobox();
							})
							.click(function(){
								CS.goToCity(this);
							});
					}
				}
			}
		}

		// Reset the shownCities array
		shownCities = [];
	});

	// Every time data changes...
	CS.DATA.on('value', function(data){

		// Update user cash in localStorage (in case it has changed)
		if ( CS.LOGGED_IN ) {
			localStorage.setItem('USER.cash', data.child('users').child(CS.USER).child('cash').val());
		}
	})
}