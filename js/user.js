(function(){

	function showUserModule() {
		// If user is not logged in, redirect to the main map
		if (!LOGGED_IN || LOGGED_IN === 'false') { 
			location = BASE;

		// If logged in...
		} else {
			DATA.once('value', function(data){

				// Show the user's name
				document.getElementById('user-name').innerHTML = data.child('users').child(USER).child('name').val();

				// Get all the cities, and create an empty array to hold the user's cities
				var cities = data.child('cities').val(),
					userCities = [];
				for (var city in cities) {
					if (cities[city].user === +USER) {
						// Add a slug key to the cities[city] object
						cities[city].slug = city;
						userCities.push(cities[city]);
					}
				}
				
				var citiesContainer = document.getElementById('cities');
				for (var i = 0; i < userCities.length; i++) {
					var li = document.createElement('li');
					li.innerHTML = '<a href="' + BASE + '/city/#/' + userCities[i].slug + '">' + userCities[i].name + '</a> (Pop: ' + commas(userCities[i].population) + ')';
					citiesContainer.appendChild(li);
				}
			});
		}
	}

	document.addEventListener('DOMContentLoaded', showUserModule);

})();