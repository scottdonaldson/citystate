function defaultModel(req) {

	var output = {
		bodyClass: '',
		title: 'City/State',
		version: '0.0.1',
		user: req.user || null
	};

	if ( req.user && req.user.admin ) output.isAdmin = true;

	return output;
}

module.exports = defaultModel;