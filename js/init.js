// Create a login reference
CS.AUTH = new FirebaseSimpleLogin(CS.DATA, function(error, user) {
	CS.loginStateChange(error, user);
});

CS.loginStateChange = function(error, user) {
	if (error) {
		CS.handleError(error, user);
	} else if (user) {
		CS.loggedIn(user);
	} else {
		CS.logOut(error);
	}
}

CS.handleError = function(error, user) {
	console.log(error);
}

CS.loggedIn = function(user) {
	// If just logging in, reload the page
	if (!CS.LOGGED_IN || CS.LOGGED_IN === 'false') {
		location.reload();
	}
	// Set global variables and localStorage
	CS.LOGGED_IN = true;
	localStorage.setItem('LOGGED_IN', true);
	CS.USER = user.id;
	localStorage.setItem('USER', user.id);

	// If the user is not already present in our DATA, add them
	CS.DATA.once('value', function(data){
		if ( !data.child('users').child(CS.USER).val() ) {
			CS.DATA.child('users').child(CS.USER).set({
				first_name: user.first_name,
				cash: 10000,
				name: user.name
			});
		}
	});
}

CS.logOut = function(error) {
	// Reset globals and localStorage
	CS.LOGGED_IN = false;
	localStorage.setItem('LOGGED_IN', CS.LOGGED_IN);
	CS.USER = false;
	localStorage.setItem('USER', CS.USER);
}

// We define a function showLoggedModule() differently
// depending on whether the user is logged in or not.
CS.showLoggedModule = function(module) {
	// Here showLoggedModule just needs to show the login button
	if (!CS.LOGGED_IN || CS.LOGGED_IN === 'false') { 

		var logButton = document.createElement('button');
		logButton.innerHTML = 'Log in';
		logButton.addEventListener('click', function(){
			CS.AUTH.login('facebook');
		});

		module.appendChild(logButton);

	// If logged in, showLoggedModule is a little more complicated.
	// We need a logout button (that, when clicked, resets globals and localStorage)
	} else {
		
		var logButton = document.createElement('button');
		logButton.innerHTML = 'Log out';
		logButton.addEventListener('click', function() {
			CS.AUTH.logout();

			// Refresh the page
			location.reload();
		});

		// And, once the data on the user is ready, return some information about them:
		// Name (linking to their user page), cash
		CS.DATA.once('value', function(data) {
			var userInfo = document.createElement('p');
			userInfo.innerHTML = '<a href="/user">' + data.child('users').child(CS.USER).child('name').val() + '</a>';
			userInfo.innerHTML += '<br>';
			userInfo.innerHTML += 'Cash: <span id="cash">' + CS.commas( data.child('users').child(CS.USER).child('cash').val() ) + '</span>';

			module.appendChild(userInfo);
			module.appendChild(logButton);
		});

		// Update the user's cash on any value changes			
		CS.DATA.on('value', function(data) {
			CS('#cash').innerHTML = CS.commas( data.child('users').child(CS.USER).child('cash').val() );
		});
	}
}

document.addEventListener('DOMContentLoaded', function() {
	CS.showLoggedModule( document.getElementById('logged-module') );
});