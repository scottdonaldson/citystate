function encode(obj) {

	var output = obj;

	if ( output.constructor == Number ) return { N: output.toString() };
	if ( output.constructor == String ) return { S: output.toString() };
	if ( output.constructor == Boolean ) return { BOOL: output };

	if ( output.constructor == Array ) {
		
		return {
			L: output.map(encode)
		};

	} else if ( output.constructor == Object ) {
		
		var temp = {};
		
		for ( var key in output ) {
			temp[key] = encode(output[key]);
		}

		return {
			M: temp
		};
	}

	return output;
}

module.exports = encode;