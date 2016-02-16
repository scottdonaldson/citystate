var _ = require('lodash'),
	defaultModel = require('../models/default');

function init(router, db) {

	router.get('/user/:id', function(req, res, next) {

		// query first to see if user exists
		db.getUser(req.params.id, function(user) {

			var model = defaultModel(req);

			res.render('user', _.assign(model, {
				viewUser: user,
				isCurrentUser: model.user ? model.user.id === user.id : false
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