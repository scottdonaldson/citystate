jQuery(document).ready(function($){

	var body = $('body');

	// Ability to remove alert
	var alert = $('#alert');
	function closeAlert() {
		$('#alert').hide();
	}
	body.on('click', '.close-box', closeAlert);

	// ----- BUDGET
	// Alert if total expenses are greater than total income
	/* $.ajax({
		url: 'budget',
		success: function(data){
			var data = $(data),
				warning = data.find('#budget_warning'),
				stateIncome  = parseInt(noCommas(data.find('.state .income span')[0].innerHTML)),
				stateExpense = parseInt(noCommas(data.find('.state .expense span')[0].innerHTML));

			if (warning.attr('checked') && stateExpense > stateIncome) {
				body.prepend('<div id="alert">WARNING: State expenses are greater than state income. <a href="budget">Update the budget.</a><div class="close-box">[ESC]</div></div>');
			}

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
		}
	});
	*/

	/* ------------- SNAPSHOT LINKS -------- */

	var snapshot = $('.snapshot');
	snapshot.click(function(e){
		$.ajax({
			url: $(this).attr('href'),
			success: function(data){
				$('html, body').animate({ scrollTop: 0 });
				$('#alert').remove(); // hide any alerts that are out there

				var data = $(data),
					info = data.find('#snapshot')[0].innerHTML;

				body.prepend('<div id="alert">'+info+'<div class="close-box">[ESC]</div></div>');	

				$('.close-box').on('click', function(){ $('#alert').hide(); });	
			}
		});
		e.preventDefault();
	});

	/* ---------------- BUILD -------------- */

	var tile = $('.tile').not('.city').filter(function(){ 
			$this = $(this);
			// Don't count tiles in neighbor maps
			return $this.closest('.neighbor').length === 0 &&
				   // Or tiles with cities in them
				   $this.find('a').length === 0;
		}),
		build = $('#build'),
		buildCity = $('.home #build'),
		structure = $('.structure'),
		extra = $('#extra');

	// Closing infoboxes and the alert
	build.append('<div class="close-box">[ESC]</div>');
	extra.append('<div class="close-box">[ESC]</div>');
	var alert = $('#alert');
	alert.append('<div class="close-box">[ESC]</div>');
	
	function close() {
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

			// Ports and fisheries can only be built on the waterfront
			var waterfront = $('#port, #fishery');
			waterfront.hide();
			if ($('.terrain.water').length > 0) {
				// North
				if ($this.data('y') == 1 && $('#n').hasClass('water')) {
					waterfront.show();
				}
				// East
				if ($this.data('x') == 0 && $('#e').hasClass('water')) {
					waterfront.show();
				}
				// South
				if ($this.data('y') == 10 && $('#s').hasClass('water')) {
					waterfront.show();
				}
				// West
				if ($this.data('x') == 1 && $('#w').hasClass('water')) {
					waterfront.show();
				}
			}

			// Show location and terrain type for user reference
			if ($this.data('x') == 0) { 
				build.find('.x').text('10'); 
			} else {
				build.find('.x').text($this.data('x'));
			}
			build.find('.y').text($this.data('y'));
			build.find('.terrain').text($this.data('terrain'));

			// Set location values if not selecting
			if (!body.hasClass('selecting')) {
				$('#x, #build-x, #scout-x').val($this.data('x'));
				$('#y, #build-y, #scout-y').val($this.data('y'));
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
	structure.on('click', function(){

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
			if (structure.data('structure') == 'neighborhood') {
				if (structure.data('level') == 1) {
					var addedCost = ' + 1 Food, 1 Fish, and 1 Wool';
				} else if (structure.data('level') == 0) {
					var addedCost = ' + 1 Food and 1 Fish';
				}
			}
			upgrade.show()
				.find('input[type="submit"]').val('Upgrade (' + $this.data('cost') + addedCost + ')');
		}

		// Fill in structure name and location for user reference
		var name = structure.data('structure');
		name = name.replace(/_/, ' '); // replace underscores with spaces

		extra.find('.name-structure').text(name);
		if (structure.attr('data-x') == 0) { 
			extra.find('.x').text('10'); 
		} else {
			extra.find('.x').text($this.attr('data-x'));
		}
		extra.find('.y').text(structure.attr('data-y'));

		// Set form location values and structure/id values
		$('#build-x, #demolish-x, #upgrade-x').val(structure.attr('data-x'));
		$('#build-y, #demolish-y, #upgrade-y').val(structure.attr('data-y'));
		$('#demolish-structure, #upgrade-structure').val(structure.attr('data-structure'));
		$('#demolish-id, #upgrade-id').val($this.attr('data-id'));
	});

	// Building 2x2 structures
	$('#stadium, #farm, #pasture').click(function(){
		var placeholder = $(this).attr('id').toLowerCase();

		// Show the helper text
		$('.helper').html('To build a '+placeholder+', now select 3 more tiles to make a 2x2 square.');
		
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
				$('.helper').html('Make sure you&apos;re building within a 2x2 square.');
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

});