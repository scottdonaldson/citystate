var config = require('../config'),
	decode = require('./decode');

function wrapper(dynamo) {

	function getOne(tablename) {
		return function(id, success, error) {
			dynamo.getItem({
				Key: {
					id: {
						S: id
					}
				},
				TableName: config.tables[tablename]
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
	function updateOne(tablename) {
		return function(data, success, error) {
			dynamo.updateItem({
				Key: {
					id: {
						S: data.id
					}
					// OTHER DATA???
				},
				TableName: config.tables[tablename]
			}, function(err, data) {
				if (err || !data.Item) return error(err);
				if (data) return success(decode(data));
			});
		};
	}

	return {
		getUser: getUser,
		getCity: getCity,
		getRegion: getRegion
	};
}

module.exports = wrapper;