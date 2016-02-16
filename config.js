var tables =  {
	users: 'Users',
	cities: 'Cities',
	regions: 'Regions'
};

function init(mode) {

	for ( var key in tables ) {
		if ( mode === 'DEV' ) {
			tables[key] += 'Dev';
		}
	}

	return {
		tables: tables
	};
}

module.exports = init;