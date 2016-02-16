function init(router, db) {

	router.get('/api/users', function(req, res, next) {

		var json = res.json.bind(res);
		db.scanUsers(['id', 'admin'], json, json);

	});
}

module.exports = init;