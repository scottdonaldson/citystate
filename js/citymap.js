var tileWidth = 40;

function buildStructureForm() {
	return 'asdf';
}

function showStructure(x, y) {

}

function showCityTiles(data, map) {
	console.log(data);
	for (var x = 0; x < 10; x++) {
		for (var y = 0; y < 10; y++) {
			var tile = map.path('M' + (tileWidth * x) + ' ' + (tileWidth * y) + ' h '+tileWidth+' v '+tileWidth+' h -'+tileWidth+' Z')
				.attr({ 
					'class': 'tile sand'
				})
				.click(function(e){
					showInfobox(e, buildStructureForm());
				});
			// Is there a structure at this tile?
			if (data.tiles.indexOf((x + 1) + ', ' + (y + 1)) > -1) {
				(function(){
					for (var structure in data.structures) {
						for (var i = 0; i < data.structures[structure].length; i++) {
							var s = data.structures[structure][i];
							if (x + 1 === s.x && y + 1 === s.y) {
								buildStructure(structure, s.level, s.x, s.y, map);
								return;
							}
						}
					}
				})();
			}
		}
	}
}

function showCityMap() {
	Snap.ajax('http://' + window.location.host + window.location.pathname + '/?snapshot=true', function(data) {
		showCityTiles(JSON.parse(data.response), Snap('#map'));
	});
}