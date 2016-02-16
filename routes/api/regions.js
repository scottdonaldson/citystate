var _ = require('lodash'),
	loggedIn = require('../../helpers/loggedin'),
	json = require('../../utils/json');

function init(router, db) {

	router.get('/api/region/:id', function(req, res, next) {

		var out = json(res);
		db.getRegion(req.params.id, out, out);

	});

	router.post('/api/region/:id', loggedIn, function(req, res, next) {

		var id = req.params.id,
			out = json(res);

		db.getRegion(id, function(data) {

			// get existing tiles
			var tiles = data.tiles,
				indices = {};

			// serialize into indices
			tiles.forEach(function(tile, i) {
				indices[tile.x + ',' + tile.y] = i;
			});

			// locate and transform tile
			if ( req.body.tiles ) {
				req.body.tiles.forEach(function(tile) {
					
					var index = indices[tile.x + ',' + tile.y];

					tile.x = +tile.x;
					tile.y = +tile.y;
					
					if ( index ) {
						tiles[index] = _.assign(tiles[index], tile);
					} else {
						tiles.push(tile);
					}
				});
			}

			db.updateRegion({
				id: id,
				tiles: tiles
			}, out, out);
		}, out);
	});

	router.get('/api/regions', function(req, res, next) {

		var out = json(res);
		db.scanRegions(['id', 'name', 'tiles', 'cities', 'x', 'y'], out, out);

	});
}

module.exports = init;