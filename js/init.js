// If user is not logged in, set up ability to do so
if (!LOGGED_IN) {

	function logIn(error, user) {
		
		// Overwrite global variables and set localStorage
		LOGGED_IN = true;
		localStorage.setItem('LOGGED_IN', true);
		USER = user.id;
		localStorage.setItem('USER', user.id);

		// If the user is not already present in our DATA, add them
		if (!DATA.child('users').child(USER)) {
			DATA.child('users').child(USER).set({
				first_name: user.first_name,
				name: user.name
			});
		// otherwise, update their record (in case anything's changed)
		} else {
			DATA.child('users').child(USER).update({
				first_name: user.first_name,
				name: user.name
			});
		}	
	}

	var auth = new FirebaseSimpleLogin(DATA, logIn);
}