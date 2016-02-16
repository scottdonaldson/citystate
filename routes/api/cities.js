function init(router, db) {

	router.get('/api/cities', function(req, res, next) {

		var json = res.json.bind(res);
		db.scanCities(['slug', 'user'], json, json);

	});
}

module.exports = init;