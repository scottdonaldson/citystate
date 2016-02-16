function is(test) {
	return function isThis(x) {
		return x === test;
	};
}

module.exports = is;