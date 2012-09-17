jQuery(document).ready(function($){

	var body = $('body');

	/* ---------------- BUILD -------------- */

	var build = $('#build'),
		buildCity = $('.home #build');
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
	$(document).keydown(function(e){
		if (e.keyCode == 27) {
			build.hide();
			$('#alert').hide();
		}
	});


	// ----- BUDGET
	var budget = $('#budget'),
		taxes = [],
		upkeep = [];
	
	// Total taxes
	budget.find('.taxes').each(function(){
		taxes.push($(this).text().replace(/,/g, ''));
	});
	totalTaxes = 0;
	for (var i = 0; i < taxes.length; i++) {
	    totalTaxes += parseInt(taxes[i]);
	}
	budget.find('.total-taxes strong').text(addCommas(totalTaxes));	
	
	// Total upkeep
	budget.find('.upkeep').each(function(){
		upkeep.push($(this).text().replace(/,/g, ''));
	});
	totalUpkeep = 0;
	for (var i = 0; i < upkeep.length; i++) {
	    totalUpkeep += parseInt(upkeep[i]);
	}
	budget.find('.total-upkeep strong').text(addCommas(totalUpkeep));	

	// Grand total
	budget.find('.grand strong').text(addCommas(totalTaxes+totalUpkeep));


	// ----- User page
	var container = $('.container'),
		header = container.find('.header');

	header.each(function(){
		$(this).on('click',function(){
			$this = $(this);
			$this.toggleClass('active').next().slideToggle();
		});
	})

	var colorInput = $('#color'),
		color = colorInput.val();
	colorInput.css({
		'background': color,
		'color': 'transparent',
	});
	$('#colorpicker').farbtastic(colorInput);

});