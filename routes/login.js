var _ = require('lodash'),
	defaultModel = require('../models/default')();

function init(router, passport) {

	router.get('/login', function(req, res, next) {
		res.render('index', _.assign(defaultModel, { alert: 'Bad login. Try again.', bodyClass: 'ocean' }));
	});

	router.post(
		'/login', 
		passport.authenticate('local', { failureRedirect: '/login' }), 
		function(req, res, next) {
			res.redirect('/');
		});

}

module.exports = init;