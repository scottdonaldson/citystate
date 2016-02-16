function defaultModel(req) {

	var output = {
		bodyClass: '',
		title: 'City/State',
		version: '0.0.1',
		user: req.user || null
	};

	output.loggedIn = req.user ? req.user.id : false;

	return output;
}

module.exports = defaultModel;