(function(){

	showUserModule() {
		// If user is not logged in, redirect to the main map
		if (!LOGGED_IN || LOGGED_IN === 'false') { 
			location = BASE;

		// If logged in...
		} else {
			// Look for a user ID
			SLUG = parseSlug('user');
		}
	}

})();