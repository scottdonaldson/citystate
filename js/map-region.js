function go_to_city() {

}

function show_tile(tile, x, y, map) {
	map.path('M' + (60 * x) + ' ' + (60 * y) + ' h50 v 50 h-50 Z');
}

function show_region_map() {
	Snap.ajax('http://' + window.location.host + window.location.pathname + '?snapshot=true', function(data) {
		var tiles = JSON.parse(data.response);
		tiles = tiles.tiles;
		for (var x = 0; x < tiles.length; x++) {
			for (var y = 0; y < tiles[x].length; y++) {
				if (tiles[x][y] !== 'water') {
					show_tile(tiles[x][y], x, y, Snap('#map'));
				}
			}
		}
	});
}