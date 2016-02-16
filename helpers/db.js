var decode = require('./decode'),
	encode = require('./encode');

function wrapper(dynamo, mode) {

	var config = require('../config')(mode);

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

	function updateOne(name) {
		return function(data, success, error) {

			var params = {};

			var id = data.id;
			delete data.id;
			
			for ( var key in data ) {
				params[key] = {
					Value: encode(data[key])
				};
			}

			dynamo.updateItem({
				Key: {
					id: {
						S: id
					}
				},
				TableName: table(name),
				AttributeUpdates: params
			}, function(err, data) {
				if (err || !data.Item) return error(err);
				if (data) return success(decode(data));
			});
		};
	}

	var updateUser = updateOne('users'),
		updateCity = updateOne('cities'),
		updateRegion = updateOne('regions');

	function add(name) {
		return function(data, success, error) {
			
			var params = {};

			for ( var key in data ) {
				params[key] = encode(data[key]);
			}

			dynamo.putItem({
				Item: params,
				TableName: table(name),
			}, function(err, data) {
				if (err) return error(err);
				if (data) return success(decode(data));
			});
		};
	}

	var addUser = add('users'),
		addCity = add('cities'),
		addRegion = add('regions');

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
		addUser: addUser,
		addCity: addCity,
		addRegion: addRegion,
		scanUsers: scanUsers,
		scanCities: scanCities,
		scanRegions: scanRegions,
		updateCity: updateCity,
		updateUser: updateUser,
		updateRegion: updateRegion
	};
}

module.exports = wrapper;