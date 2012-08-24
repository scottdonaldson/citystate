jQuery(document).ready(function($){

	$('.draggable').draggable();

	var body = $('body');

	/* ---------------- BUILD -------------- */

	var build = $('#build'),
		buildCity = $('.home #build'),
		buildStructure = $('.single #build');
	$('.tile').click(function(){
		$this = $(this);

		// First, test for conditions that say "don't build here"
		if (	
				$this.find('div').hasClass('city') || 
				$this.hasClass('no-build') || 
				$this.hasClass('water')

			// If not, then let's go	
			) { } else {

			// If build already is active, clear its values and rehide form
			if (build.hasClass('active')) {
				build.find('input[type!="submit"]').val('');
				buildCity.find('form').hide();
			}

			// Add a class of active to the build box
			build.addClass('active');

			// Determine position and show box
			var position = $this.position();
			build.show().css({
				'left': position.left + 30,
				'top' : position.top + 30
			});

			// Set location values
			$('#x').val($this.data('x'));
			$('#y').val($this.data('y'));
		}
	});

	// City-specific
	buildCity.find('h2').click(function(){
		$(this).next('form').slideToggle();
	})

	// Structure-specific
	buildStructure.find('input[type="radio"]').click(function(){
		$this = $(this);
		$('#structure').val($this.attr('id'));
		$('#structure-cost').val($this.data('cost'));
	});
	$(document).keydown(function(e){
		if (e.keyCode == 27) {
			build.hide();
		}
	});
	

});