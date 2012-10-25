jQuery(document).ready(function($){

	var body = $('body'),
		alert = $('#alert');

	/* ---------------- BUILD -------------- */

	var tile = $('.tile'),
		build = $('#build'),
		buildCity = $('.home #build'),
		structure = $('.structure'),
		extra = $('#extra');

	// Closing infoboxes and the alert
	build.append('<div class="close-box">[ESC]</div>');
	extra.append('<div class="close-box">[ESC]</div>');
	alert.append('<div class="close-box">[ESC]</div>');
	var close = function(){
		// Close infobox or alert
		$('.infobox').removeClass('active').find('ul li').removeClass('chosen');
		$('#build-structure').val('');
		$('#alert').hide();

		// Remove any helper text
		$('.helper').html('');

		// Deactivate all tiles
		tile.removeClass('tile-0 tile-1 tile-2 tile-3 footprint');

		// Reset count
		var count = 0;
	}
	$('.close-box').on('click', close);	
	// ----- Press ESC
	$(document).keydown(function(e){
		if (e.keyCode == 27) {
			close();
		}
	});

	var count = 0; // Used in adding new inputs for extra location values (2x2 structures)
	tile.on('click',function(){
		$this = $(this);

		// Remove all active footprints if not selecting
		if (!body.hasClass('selecting')) {
			$this.siblings().removeClass('footprint');
		}

		// First, test for conditions that say "don't build here"
		if (	
			$this.find('div').hasClass('city') || 
			$this.hasClass('no-build') || 
			$this.hasClass('water')

		// If none, then let's go	
		) { } else {

			// Show footprint, remove all other footprints
			if (!body.hasClass('selecting')) {
				$this.addClass('footprint').siblings().removeClass('footprint');
				// Remove tile-0 if it already exists
				tile.removeClass('tile-0'); 
			}

			// Hide extra (demolish/upgrade) infobox
			extra.removeClass('active');

			// If build already is active, clear its values and rehide form
			// (only if not selecting other tiles for large structure)
			if (build.hasClass('active') && !body.hasClass('selecting')) {
				build.find('input[type!="submit"]').val('');
			}

			// Add a class of active to the build box and show it
			build.addClass('active').show();

			// Show location for user reference
			build.find('.x').text($this.data('x'));
			build.find('.y').text($this.data('y'));

			// Set location values if not selecting
			if (!body.hasClass('selecting')) {
				$('#x, #build-x').val($this.data('x'));
				$('#y, #build-y').val($this.data('y'));
			// Otherwise, add new inputs for extra location values
			} else {
				count++;

				// Only select up to four squares (for 2x2 structure)
				if (count < 4) {
					$('#x').after('<input type="hidden" id="x-'+count+'" value="'+$this.data('x')+'" />');
					$('#build-x').after('<input type="hidden" id="build-x-'+count+'" name="build-x-'+count+'" value="'+$this.data('x')+'" />');
					$('#y').after('<input type="hidden" id="y-'+count+'" value="'+$this.data('y')+'" />');
					$('#build-y').after('<input type="hidden" id="build-y-'+count+'" name="build-y-'+count+'" value="'+$this.data('y')+'" />');
				// Once we reach 4, we're going to cycle through the previously
				// selected tiles. Deactivate those footprints and continue cycling.
				} else {
					var oldTile = $('.tile-'+count%4);

					// Remove footprint from old tile
					oldTile.removeClass('tile-'+count%4+' footprint');	

					if (count%4 === 0) {
						$('#build-x').val($this.data('x'));
						$('#build-y').val($this.data('y'));	
					} else {
						$('#build-x-'+count%4).val($this.data('x'));
						$('#build-y-'+count%4).val($this.data('y'));		
					}
				}
			// End are we selecting?
			}

			// Let's call this tile-0
			// And if we're selecting more for a 2x2 structure, tile-count%4
			$this.addClass('tile-'+count%4);

		// End can we build here?	
		}
	});
	build.find('ul li').on('click',function(){
		$this = $(this);
		$this.addClass('chosen').siblings('li').removeClass('chosen');
		build.find('input[name="build-structure"]').val($this.attr('id'));

		// By default, assume that we're NOT building a 2x2 structure
		body.removeClass('selecting');
		$('.helper').html('');
		$('.tile-1, .tile-2, .tile-3').removeClass('footprint');
	});
	
	// ----- Extra: Demolish/Upgrade	
	var demolish = $('.demolish'),
		upgrade = $('.upgrade');
	structure.on('click',function(){

		var structure = $(this);

		// Hide build infobox
		build.hide();

		// If were selecting, remove the selecting class and all footprints
		body.removeClass('selecting');
		tile.removeClass('footprint');

		// If it was being shown elsewhere, clear form values and hide upgrade box
		if (extra.hasClass('active')) {
			extra.find('input[type!="submit"]').val('').attr('checked', false);
			extra.find('.upgrade').hide();
		}

		// Add a class of active to the extra box and show it
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

		// Set form location values and structure/id values
		$('#build-x, #demo-x, #upgrade-x').val(structure.data('x'));
		$('#build-y, #demo-y, #upgrade-y').val(structure.data('y'));
		$('#demo-structure, #upgrade-structure').val(structure.data('structure'));
		$('#demo-id, #upgrade-id').val($this.data('id'));
	});

	// Building stadiums (2x2 structures)
	$('#stadium').click(function(){
		// Show the helper text
		$('.helper').html('To build a stadium, now select 3 more tiles to make a 2x2 square.');
		
		// We are definitely selecting more tiles
		body.addClass('selecting');
		$('.selecting .tile').on('click',function(){
			$(this).addClass('footprint');
		});
	});
	build.find('form').submit(function(){
		if (body.hasClass('selecting')) {
			var arrX = [$('.tile-0').data('x'), $('.tile-1').data('x'), $('.tile-2').data('x'), $('.tile-3').data('x')];
			var arrY = [$('.tile-0').data('y'), $('.tile-1').data('y'), $('.tile-2').data('y'), $('.tile-3').data('y')];

			// Find the lowest and highest values in both X and Y arrays
			var lowX = Math.min.apply( Math, arrX ),
				lowY = Math.min.apply( Math, arrY ),
				hiX  = Math.max.apply( Math, arrX ),
				hiY  = Math.max.apply( Math, arrY );

			// Conditions for not building:
			// Not in a square / haven't clicked enough to make a square
			if (hiX > lowX + 1 || hiY > lowY + 1 || count < 3) {
				$('.helper').html('Make sure you&apos;re building that stadium within a 2x2 square.');
				return false;
			}
		}
		if ($('#build-structure').val() == '') {
			$('.helper').html('Pick a structure to build.')
			return false;
		}
	});

	// ----- UPDATING USER PROFILE

	var profile = $('.profile'),
		section = profile.find('section');
	
	section.find('h3').each(function(){
		$this = $(this);
		$this.nextAll().hide();
		$(this).on('click',function(){
			$this = $(this);
			$(this).toggleClass('chosen').nextAll().toggle();

			var chosen = $('.chosen').length;
			if (chosen > 0) {
				profile.find('.submit').show();
			} else if (chosen === 0) {
				profile.find('.submit').hide();
			}
		});
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