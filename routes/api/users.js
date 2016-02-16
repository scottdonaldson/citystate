var json = require('../../utils/json');

function init(router, db) {

	router.get('/api/user/:id', function(req, res, next) {

		var out = json(res);
		db.getUser(req.params.id, out, out);

	});

	router.get('/api/users', function(req, res, next) {

		var out = json(res);
		db.scanUsers(['id', 'admin'], out, out);

	});
}

module.exports = init;