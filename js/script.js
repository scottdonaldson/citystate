jQuery(document).ready(function($){

	var body = $('body');

	/* ---------------- BUILD -------------- */

	var build = $('#build'),
		buildCity = $('.home #build'),
		structure = $('.structure'),
		extra = $('#extra');

	$('.tile').click(function(){
		$this = $(this);

		// First, test for conditions that say "don't build here"
		if (	
				$this.find('div').hasClass('city') || 
				$this.hasClass('no-build') || 
				$this.hasClass('water')

			// If not, then let's go	
			) { } else {

			extra.hide();

			// If build already is active, clear its values and rehide form
			if (build.hasClass('active')) {
				build.find('input[type!="submit"]').val('');
			}

			// Add a class of active to the build box and show it
			build.addClass('active').show();

			// Show location for user reference
			build.find('.x').text($this.data('x'));
			build.find('.y').text($this.data('y'));

			// Set location values
			$('#x, #build-x').val($this.data('x'));
			$('#y, #build-y').val($this.data('y'));
		}
	});

	// City-specific
	buildCity.find('h2').click(function(){
		$(this).next('form').slideToggle();
	});
	
	// ----- Extra: Demolish/Upgrade	
	var demolish = $('.demolish'),
		upgrade = $('.upgrade');
	structure.on('click',function(){
		var structure = $(this);

		build.hide();

		// If it was being shown elsewhere, clear form values and hide upgrade box
		if (extra.hasClass('active')) {
			extra.find('input[type!="submit"]').val('').attr('checked', false);
			extra.find('.upgrade').hide();
		}

		// Add a class of active to the build box and show it
		extra.addClass('active');		

		// If structure can be upgraded, show upgrade box
		if (structure.data('upgrade') == 1) {
			upgrade.show();
			upgrade.find('input[type="submit"]').val('Upgrade (' + $this.data('cost') + ')');
		}

		// Fill in structure name and location for user reference
		extra.find('.name-structure').text(structure.data('structure'));
		extra.find('.x').text(structure.data('x'));
		extra.find('.y').text(structure.data('y'));

		// Set form location values
		$('#build-x, #demo-x, #upgrade-x').val(structure.data('x'));
		$('#build-y, #demo-y, #upgrade-y').val(structure.data('y'));

		// Set structure
		extra.on('click',function(){
			$('#demo-structure, #upgrade-structure').val(structure.data('structure'));
			$('#demo-id, #upgrade-id').val($this.data('id'));
		});
	});

	$(document).keydown(function(e){
		if (e.keyCode == 27) {
			$('.infobox').removeClass('active');
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