var tileWidth = 40;

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
	return output;
}

function showStructure(i, j, map) {
	return map.path('M ' + (tileWidth * (cities[i].x - 1) + 4 * (cities[i].structures[j].x - 1) + 1) + ' ' + (tileWidth * (cities[i].y - 1) + 4 * (cities[i].structures[j].y - 1) + 1) + ' h2 v2 h-2 Z')
		.attr({ fill: '#444' });
}

function showCityInfo(info) {
	return '<strong>' + info.data('city-name') + '</strong><br>Pop: ' + commas(info.data('city-population'));
}

function goToCity(info) {
	window.location = window.location.href + info.data('city-slug');
}

function showWorldMap() {
	// Use the global world and cities objects and create the map
	var map = Snap('#map');
	for (var x = 0; x < world.tiles.length; x++) {
		for (var y = 0; y < world.tiles[x].length; y++) {
			if (world.tiles[x][y] !== 'water') {
				var tile = map.path('M' + (tileWidth * x) + ' ' + (tileWidth * y) + ' h '+tileWidth+' v '+tileWidth+' h -'+tileWidth+' Z')
					.attr({ 
						'class': 'tile ' + world.tiles[x][y]
					})
					.data('terrain', world.tiles[x][y])
					.data('has-city', false);

				// Check to see if there's a city at this tile
				for (var i = 0; i < cities.length; i++) {
					if (cities[i].x === x + 1 && cities[i].y === y + 1) {
						for (var j = 0; j < cities[i].structures.length; j++) {
							showStructure(i, j, map);
						}
						tile.data('has-city', true);
						var facade = tile.clone().attr({'class': 'tile', 'fill': 'transparent'});

						facade.data('city-name', cities[i].name);
						facade.data('city-slug', cities[i].slug);
						facade.data('city-population', cities[i].population);

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
						showInfobox(e, buildCityForm(this.data('terrain'), world));
					});
				}
			}
		}
	}
}