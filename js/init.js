// We define a function showLoggedModule() differently
// depending on whether the user is logged in or not.

// If logged out...
if (!LOGGED_IN || LOGGED_IN === 'false') {

	// Function to log in that is called when
	// the user clicks on the forthcoming login button
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

		// Refresh the page
		window.location.reload();
	}

	// Here showLoggedModule just needs to show the login button
	function showLoggedModule(module) {

		var logButton = document.createElement('button');
		logButton.onclick = function(){
			var auth = new FirebaseSimpleLogin(DATA, logIn);
			auth.login('facebook');
		}
		logButton.innerHTML = 'Log in';

		module.appendChild(logButton);
	}

// If user is logged in...
} else {

	// In this case, showLoggedModule is a little more complicated.
	// We need a logout button (that, when clicked, resets globals and localStorage)
	function showLoggedModule(module) {
		var logButton = document.createElement('button');
		logButton.innerHTML = 'Log out';
		logButton.onclick = function() {
			LOGGED_IN = false;
			localStorage.setItem('LOGGED_IN', LOGGED_IN);
			USER = false;
			localStorage.setItem('USER', USER);

			// Refresh the page
			window.location.reload();
		};

		// And, once the data on the user is ready, return some information about them:
		// Name (linking to their user page), cash
		DATA.once('value', function(data) {
			var userInfo = document.createElement('p');
			userInfo.innerHTML = '<a href="/user">' + data.child('users').child(USER).child('name').val() + '</a>';
			userInfo.innerHTML += '<br>';
			userInfo.innerHTML += 'Cash: ' + data.child('users').child(USER).child('cash').val();

			module.appendChild(userInfo);
			module.appendChild(logButton);
		});
	}
}

document.addEventListener('DOMContentLoaded', function() {
	showLoggedModule( document.getElementById('logged-module') );
});