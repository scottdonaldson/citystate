var _ = require('lodash'),
	defaultModel = require('../models/default');

function init(router, db) {

	router.get('/user/:id', function(req, res, next) {

		// query first to see if user exists
		db.getUser(req.params.id, function(data) {

			res.render('user', _.assign(defaultModel(req), {
				viewUser: data
			}));

		}, function(err) {
			
			res.status(400).render('error', {
				message: '404 - User Not Found',
				error: {}
			});

		});
	});

}

module.exports = init;