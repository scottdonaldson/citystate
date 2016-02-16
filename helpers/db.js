function wrapper(dynamo, mode) {

	var config = require('../config')(mode),
		decode = require('./decode');

	function table(name) {
		return config.tables[name];
	}

	function getOne(name) {
		return function(id, success, error) {
			dynamo.getItem({
				Key: {
					id: {
						S: id
					}
				},
				TableName: table(name)
			}, function(err, data) {
				if (err || !data.Item) return error(err);
				if (data) return success(decode(data));
			});
		};
	}

	var getUser = getOne('users'),
		getCity = getOne('cities'),
		getRegion = getOne('regions');

	// TODO
	function updateOne(name) {
		return function(data, success, error) {
			dynamo.updateItem({
				Key: {
					id: {
						S: data.id
					}
					// OTHER DATA???
				},
				TableName: table(name)
			}, function(err, data) {
				if (err || !data.Item) return error(err);
				if (data) return success(decode(data));
			});
		};
	}

	function scan(name) {
		return function(data, success, error) {

			var params = {
				AttributesToGet: [],
				TableName: table(name)
			};

			if ( typeof data === 'string' ) {
				params.AttributesToGet = [data];
			} else if ( data.length > 0 ) {
				params.AttributesToGet = data;
			}

			dynamo.scan(params, function(err, data) {

				if (err || !data.Items) return error(err);
				if (data) return success(decode(data));
			});
		};
	}

	var scanUsers = scan('users'),
		scanCities = scan('cities'),
		scanRegions = scan('regions');

	return {
		_dynamo: dynamo,
		getUser: getUser,
		getCity: getCity,
		getRegion: getRegion,
		scanUsers: scanUsers,
		scanCities: scanCities,
		scanRegions: scanRegions
	};
}

module.exports = wrapper;