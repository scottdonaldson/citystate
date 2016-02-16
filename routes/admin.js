var _ = require('lodash'),
	slug = require('slug'),
	defaultModel = require('../models/default');

function init(router, db) {

	router.get('/admin', function(req, res, next) {
		
		res.render('admin', defaultModel(req));
		
	});

	router.post('/admin', function(req, res, next) {

		var name = req.body.name,
			x = req.body.x,
			y = req.body.y;

		if ( !name || !x || !y ) {
			return res.render('admin', _.assign(defaultModel(req), {
				alert: 'Must include all parameters for a new region.'
			}));
		}

		var params = {
			id: slug(name).toLowerCase(),
			name: name,
			x: +x,
			y: +y,
			tiles: [{ x: 0, y: 0, type: 'sand' }]
		};

		db.addRegion(params, function() {
			res.redirect('/');
		}, function(err) {
			res.render('admin', _.assign(defaultModel(req), {
				alert: JSON.stringify(err)
			}));
		});

	});
}

module.exports = init;