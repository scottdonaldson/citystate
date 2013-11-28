// If user is not logged in, set up ability to do so
if (!LOGGED_IN || LOGGED_IN === 'false') {
	console.log('logged out');

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

		window.location.reload();
	}

	var logButton = document.createElement('button');
	logButton.onclick = function(){
		var auth = new FirebaseSimpleLogin(DATA, logIn);
		auth.login('facebook');
	}
	logButton.innerHTML = 'Log in';

// If user is logged in, give option to log out
// and show user module
} else {
	var logButton = document.createElement('button');
	logButton.innerHTML = 'Log out';
	logButton.onclick = function() {
		LOGGED_IN = false;
		localStorage.setItem('LOGGED_IN', LOGGED_IN);
		USER = false;
		localStorage.setItem('USER', USER);

		window.location.reload();
	};

	DATA.once('value', function(data){
		document.getElementById('user').innerHTML = 'Logged in as ' + data.child('users').child(USER).child('name').val();
	});
}

window.addEventListener('load', function(){
	document.getElementById('log-in-out').appendChild(logButton);
});