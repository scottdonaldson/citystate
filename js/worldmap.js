function show_region(tiles, map) {
	for (var x = 0; x < tiles.length; x++) {
		for (var y = 0; y < tiles[x].length; y++) {
			map.path('M0 0 h 40 v 40 h -40 Z');
		}
	}
}

function show_world_map() {
	for (var i = 0; i < regions.length; i++) {
		Snap.ajax('http://' + window.location.host + window.location.pathname + '/' + regions[i].post_name + '/?snapshot=true', function(data) {
				show_region(JSON.parse(data.response).tiles, Snap('#map'));
			});
	}
}