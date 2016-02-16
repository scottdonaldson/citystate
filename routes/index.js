var _ = require('lodash'),
	express = require('express'),
	router = express.Router();

function init(passport, db) {
	
	require('./world')(router);
	require('./login')(router, passport);
	require('./user')(router, db);

	return router;
}

module.exports = init;
