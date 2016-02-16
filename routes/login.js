var _ = require('lodash'),
	defaultModel = require('../models/default');

function init(router, passport) {

	router.get('/login', function(req, res, next) {
		res.render('index', _.assign(defaultModel(req), { alert: 'Bad login. Try again.', bodyClass: 'ocean' }));
	});

	router.post(
		'/login',
		passport.authenticate('local', { 
			failureRedirect: '/login'
		}), 
		function(req, res, next) {
			res.render('index', _.assign(defaultModel(req), { 
				alert: 'You are now logged in.'
			}));
		}
	);

	router.get('/logout', function(req, res, next) {
		req.logout();
		res.redirect('/');
	});

}

module.exports = init;