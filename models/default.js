function defaultModel(req) {

	var output = {
		bodyClass: '',
		title: 'City/State',
		version: '0.0.1',
		user: req.user || null
	};

	return output;
}

module.exports = defaultModel;