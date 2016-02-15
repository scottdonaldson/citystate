var _ = require('lodash'),
	express = require('express'),
	router = express.Router();

function init(passport) {
	
	require('./world')(router);
	require('./login')(router, passport);

	return router;
}

module.exports = init;
