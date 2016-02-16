var json = require('../../utils/json');

function init(router, db) {

	router.get('/api/region/:id', function(req, res, next) {

		var out = json(res);
		db.getRegion(req.params.id, out, out);

	});

	router.get('/api/regions', function(req, res, next) {

		var out = json(res);
		db.scanRegions(['id', 'name', 'tiles', 'cities', 'x', 'y'], out, out);

	});
}

module.exports = init;