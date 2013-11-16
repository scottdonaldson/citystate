function go_to_region() {
	window.location.assign(window.location.host + window.location.pathname + '/region/' + this.data('slug'));
}

function show_region(data, map) {
	for (var x = 0; x < data.tiles.length; x++) {
		for (var y = 0; y < data.tiles[x].length; y++) {
			if (data.tiles[x][y] !== 'water') {
				var fill = data.tiles[x][y] === 'grass' ? '#9d8' :
						   data.tiles[x][y] === 'sand' ? '#db8' :
						   data.tiles[x][y] === 'forest' ? '#5a5' :
						   data.tiles[x][y] === 'hills' ? '#ab9' :
						   data.tiles[x][y] === 'mountains' ? '#999' :
						   '#000';
				map.path('M' + (6 * (10 * data.position.x + x)) + ' ' + (6 * (10 * data.position.y + y)) + ' h 6 v 6 h -6 Z').attr({ fill: fill }).data('slug', data.slug).data('terrain', data.tiles[x][y]).click(go_to_region);
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