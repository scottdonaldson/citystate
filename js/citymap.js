var tileWidth = 40;

function buildStructureForm() {
	return 'asdf';
}

function showCityTiles(data, map) {
	console.log(data);
	for (var x = 1; x <= 10; x++) {
		for (var y = 1; y <= 10; y++) {
			map.path('M' + (tileWidth * x) + ' ' + (tileWidth * y) + ' h '+tileWidth+' v '+tileWidth+' h -'+tileWidth+' Z')
				.attr({ 
					'class': 'tile sand'
				})
				.click(function(e){
					showInfobox(e, buildStructureForm());
				});
		}
	}
}

function showCityMap() {
	Snap.ajax('http://' + window.location.host + window.location.pathname + '/?snapshot=true', function(data) {
		showCityTiles(JSON.parse(data.response), Snap('#map'));
	});
}