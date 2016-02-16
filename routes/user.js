var _ = require('lodash'),
	decode = require('../helpers/decode'),
	defaultModel = require('../models/default');

function init(router, db) {

	router.get('/user/:id', function(req, res, next) {

		// query first to see if user exists
		db.getItem({
			Key: {
				id: {
					S: req.params.id
				}
			},
			TableName: 'UsersDev'
		}, function(err, data) {

			if ( err || !data.Item ) {
				return res.status(400).render('error', {
					message: '404 - User Not Found',
					error: {}
				});
			}

			if ( req.user ) {
				res.render('user', defaultModel(req));
			} else {
				data = decode(data);
				res.render('user', _.assign(defaultModel(req), {
					user: {
						id: data.id
					}
				}));	
			}
		});
	});

}

module.exports = init;