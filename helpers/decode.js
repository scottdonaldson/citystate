function decode(data) {
	
	var output = {};
	
	for ( var key in data.Item ) {
		for ( var subkey in data.Item[key] ) {
			output[key] = data.Item[key][subkey];
		}
	}

	return output;
}

module.exports = decode;