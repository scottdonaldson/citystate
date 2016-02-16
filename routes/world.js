var _ = require('lodash'),
	defaultModel = require('../models/default');

function init(router, db) {

	router.get('/', function(req, res, next) {
		db.scanRegions(['id', 'tiles', 'cities', 'x', 'y'], function(regions) {

			regions = regions.map(function(region) {
				region.tiles = region.tiles.map(function(tile) {

					tile.x *= 6;
					tile.y *= 6;
					tile.width = 6;
					tile.height = 6;

					tile.x += region.x;
					tile.y += region.y;

					return tile;
				});

				return region;
			});

			res.render('world', _.assign(defaultModel(req), { 
				bodyClass: 'ocean',
				regions: regions
			}));
		}, function(err) {
			res.render('error', _.assign(defaultModel(req), err));
		});
		
	});

}

module.exports = init;