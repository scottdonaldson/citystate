(function(){

	function showUserModule() {
		// If user is not logged in, redirect to the main map
		if (!LOGGED_IN || LOGGED_IN === 'false') { 
			location = BASE;

		// If logged in...
		} else {
			DATA.once('value', function(data){

				// Show the user's name
				$('#user-name').innerHTML = data.child('users').child(USER).child('name').val();

				// Get all the user's cities and locate container, a <ul>
				var cities = data.child('users').child(USER).child('cities').val(),
					citiesContainer = $('#cities');

				// Loop through cities and add <li> to the container with link and population	
				for (var i = 0; i < cities.length; i++) {

					var li = document.createElement('li'),
						city = data.child('cities').child(cities[i]).val();

					li.innerHTML = '<a href="' + BASE + '/city/#/' + city.slug + '">' + city.name + '</a> (Pop: ' + commas(city.population) + ')';
					citiesContainer.appendChild(li);
				}
			});
		}
	}

	document.addEventListener('DOMContentLoaded', showUserModule);

})();