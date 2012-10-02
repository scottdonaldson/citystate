jQuery(document).ready(function($){

	var body = $('body');

	/* ---------------- BUILD -------------- */

	var build = $('#build'),
		buildCity = $('.home #build'),
		structure = $('.structure'),
		demolish = $('#demolish');

	$('.tile').click(function(){
		$this = $(this);

		// First, test for conditions that say "don't build here"
		if (	
				$this.find('div').hasClass('city') || 
				$this.hasClass('no-build') || 
				$this.hasClass('water')

			// If not, then let's go	
			) { } else {

			demolish.hide();

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
				'left': position.left + 20,
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
	});
	
	// ----- Demolish	
	structure.on('click',function(){
		var structure = $(this);

		build.hide();

		// If demolish already is active, clear its values and rehide form
		if (demolish.hasClass('active')) {
			demolish.find('input[type!="submit"]').val('').attr('checked', false);
		}

		// Add a class of active to the build box
		demolish.addClass('active');		

		// Determine position and show box
		var position = $this.position();
		demolish.show().css({
			'left': position.left + 20,
			'top' : position.top + 50
		});

		// Set location values
		$('#x, #demo-x').val($this.data('x'));
		$('#y, #demo-y').val($this.data('y'));

		// Set structure
		demolish.on('click',function(){
			$(this).find('input:checked').each(function(){
				$('#demo-structure').val(structure.data('structure'));
				$('#id').val($this.data('id'));
			});
		});
	});

	$(document).keydown(function(e){
		if (e.keyCode == 27) {
			$('.infobox').hide();
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


	// ------ Containers and modules
	var container = $('.container'),
	header = container.find('.header');

	header.each(function(){
		$(this).on('click',function(){
			$this = $(this);
			$this.toggleClass('active').next().slideToggle();
		});
	});

});