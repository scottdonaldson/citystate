var _ = require('lodash');

function decode(data) {
	
	var output = data;

	var parseFunc = {
		S: collapse,
		N: num,
		B: collapse,
		BOOL: collapse,
		L: list,
		M: collapse
	};

	if ( output.Items ) output = output.Items;
	if ( output.Item ) output = output.Item;

	function shouldParse(obj) {

		var newObj = _.assign(obj);

		if ( Array.isArray(newObj) ) {
			newObj = newObj.map(shouldParse);
		}

		for ( var key in newObj ) {
			var sub = newObj[key];
			if ( Object.keys(sub).length === 1 && Object.keys(sub)[0] in parseFunc ) {
				newObj[key] = shouldParse(parseFunc[Object.keys(sub)[0]](sub));
			}
		}

		return newObj;
	}

	function collapse(obj) {
		return obj[Object.keys(obj)[0]];
	}

	function num(obj) {
		return +collapse(obj);
	}

	function list(obj) {
		var arr = obj[Object.keys(obj)[0]];
		return arr.map(shouldParse);
	}

	output = shouldParse(output);

	return output;
}

module.exports = decode;