var _ = require('lodash'),
	express = require('express'),
	router = express.Router();

function init(passport, db) {
	
	require('./world')(router, db);
	require('./login')(router, passport);
	require('./user')(router, db);
	require('./region')(router, db);

	// API modules
	require('./api/users')(router, db);
	require('./api/regions')(router, db);
	require('./api/cities')(router, db);

	return router;
}

module.exports = init;
