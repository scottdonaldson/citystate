var _ = require('lodash'),
	defaultModel = require('../models/default')();

function init(router) {

	router.get('/', function(req, res, next) {
		res.render('index', _.assign(defaultModel, { bodyClass: 'ocean' }));
	});

}

module.exports = init;