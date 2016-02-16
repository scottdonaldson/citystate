var _ = require('lodash'),
	defaultModel = require('../models/default');

function init(router, db) {

	router.get('/region/:id', function(req, res, next) {
		db.getRegion(req.params.id, function(region) {

			region.tiles = region.tiles.map(function(tile) {

				tile.x *= 60;
				tile.y *= 60;
				tile.width = 60;
				tile.height = 60;

				return tile;
			});

			res.render('region', _.assign(defaultModel(req), { 
				bodyClass: 'ocean',
				tiles: region.tiles
			}));
		}, function(err) {
			res.render('error', _.assign(defaultModel(req), err));
		});
		
	});

}

module.exports = init;