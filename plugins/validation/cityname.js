jQuery(document).ready(function($){
	
	var buildCity = $('.home #build');

	// Validate form before submitting
	buildCity.find('form').on('submit',function(){
		var cityName = $('#cityName').val(),
			$this = $(this),
			special = [ '`', '~', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_', '=', '+', '[', '{', ']', '}', '\\', '|', ';', ':', '"', '<', '>', '/', '?'];
		// Make sure the user typed something in
		if (cityName.length === 0) {
			$('.helper').remove();
			$this.append('<p class="helper">Enter a name for your city.</p>');
			return false;
		// City name must be at least 2 characters
		} else if (cityName.length == 1) {
			$('.helper').remove();
			$this.append('<p class="helper">Enter a longer name for your city. That&apos;s just ridiculous.</p>');
			return false;
		}
		// No special characters other than periods, commas, apostrophes
		for (i=0; i<special.length; i++) {
			if (cityName.indexOf(special[i]) != -1) {
				$('.helper').remove();
				$this.append('<p class="helper">City names can&apos;t contain special characters other than commas, periods, and apostrophes. Otherwise professional punctuators would be likely to riot.</p>');
				return false;
			}
		}
		// First and last characters can't be spaces
		if (cityName[0] == ' ') {
			$('.helper').remove();
			$this.append('<p class="helper">Your city can&apos;t have a name that starts with a space.</p>');
			return false;
		} else if (cityName[cityName.length - 1] == ' ') {
			$('.helper').remove();
			$this.append('<p class="helper">Your city can&apos;t have a name that ends with a space.</p>');
			return false;
		}
	});

});