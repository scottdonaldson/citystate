var tileWidth = 40;

function buildCityForm(terrain, data) {
	var output = '';
	var prep = terrain === 'forest' ? 'in' : 'on';
	var thisOrThese = terrain.slice(-1) === 's' && terrain !== 'grass' ? 'these' : 'this';
	terrain = terrain === 'grass' ? 'grassland' : terrain;
	output += '<form action="?build=true" method="POST">' + 
			  '<label>Build a city ' + prep + ' ' + thisOrThese + ' uninhabited ' + terrain + '?</label>' + 
			  '<input type="text" name="cityName" placeholder="Name your city">' + 
			  '<input type="hidden" name="region_id" value="'+ data.id +'">' + 
			  '<input type="submit" name="buildCity" value="Build">' + 
			  '</form>';
	return output;
}

function showStructure(data, i, j, map) {
	return map.path('M ' + (tileWidth * (data.cities[i].x - 1) + 4 * (data.cities[i].structures[j].x - 1) + 1) + ' ' + (tileWidth * (data.cities[i].y - 1) + 4 * (data.cities[i].structures[j].y - 1) + 1) + ' h2 v2 h-2 Z');
}

function showCityInfo(tile) {
	console.log(tile);
	return tile.data('city-name');
}

function goToCity() {
	console.log('going to city')
}

function show_region(data, map) {
	var latestCity = 0;
	for (var x = 0; x < data.tiles.length; x++) {
		for (var y = 0; y < data.tiles[x].length; y++) {
			if (data.tiles[x][y] !== 'water') {
				var tile = map.path('M' + (tileWidth * (10 * data.position.x + x)) + ' ' + (tileWidth * (10 * data.position.y + y)) + ' h '+tileWidth+' v '+tileWidth+' h -'+tileWidth+' Z')
					.attr({ 
						'class': 'tile ' + data.tiles[x][y]
					})
					.data('terrain', data.tiles[x][y])
					.data('has-city', false);

				// Check to see if there's a city at this tile
				for (var i = latestCity; i < data.cities.length; i++) {
					if (data.cities[i].x === x + 1 && data.cities[i].y === y + 1) {
						for (var j = 0; j < data.cities[i].structures.length; j++) {
							showStructure(data, i, j, map);
						}
						tile.data('has-city', true)
							.data('city-name', data.cities[i].name);
						latestCity = data.cities[i + 1];
						var facade = tile.clone().attr({'class': 'tile', 'fill': 'transparent'});
						break;
					}
				}
				// If no city, click to prompt building one
				if (!tile.data('has-city')) {
					tile.click(function(e){
						showInfobox(e, buildCityForm(this.data('terrain'), data));
					});
				// If there is a city, hover over the parent (group of tile and city)
				// to show city info and click to go to city
				} else {
					facade.hover(function(e){
							showInfobox(e, showCityInfo(tile));
						}, hideInfobox)
						.click(goToCity);
				}
			}
		}
	}
}

function show_world_map() {
	for (var i = 0; i < regions.length; i++) {
		Snap.ajax('http://' + window.location.host + window.location.pathname + '/region/' + regions[i].post_name + '/?snapshot=true', function(data) {
				show_region(JSON.parse(data.response), Snap('#map'));
			});
	}
}