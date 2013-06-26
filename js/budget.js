jQuery(document).ready(function($){
	var input = $('input[type="number"]'),
		origStateExpense = parseInt($('.state .original').data('original')),
		stateExpense = $('.state .expense span');

	(function($){
		$.fn.extend({
	        updateTotal: function() {

	        	$('.warning').remove();
	        	
	        	$this = $(this);
	        	
	        	var cityIncome = parseInt(noCommas($this.closest('.board').find('.income').text())),
		        	cityExpense = $this.closest('.board').find('.expense'),
	        		origCityExpense = parseInt($this.closest('.board').find('.original').data('original')),
		        	oldCityExpense = parseInt(noCommas(cityExpense.text())),
		        	cityNet = $this.closest('.board').find('.net'),
        			oldStateExpense = parseInt(noCommas($('.state .expense span').text())),
        			stateIncome = parseInt(noCommas($('.state .income span').text())),
        			stateNet = $('.state .net span'),
	        		original = parseInt(noCommas($this.attr('placeholder')));

	        	console.log(cityExpense);	

	        	if ($this.hasClass('f')) {
	        		oldCityExpense = origCityExpense;
	        		oldStateExpense = origStateExpense;
	        	}	

	        	var keepAtIt = setInterval(function(){
	        		var newValue = $this.attr('value');
	        		if (newValue.length == 0) { 
						newValue = original; 
					} else { 
						newValue = parseInt(newValue);
					}

					var	newCityValue = oldCityExpense + newValue - original;
					var	newStateValue = oldStateExpense + newValue - original;

					// update city expense
					cityExpense.text(addCommas(newCityValue));
					// update city net
					cityNet.text(addCommas(cityIncome - newCityValue));
					// update state expense
					stateExpense.text(addCommas(newStateValue));
					// update state net
					stateNet.text(addCommas(stateIncome - newStateValue));
	        	}, 100);

	        	// stop updating when the input loses focus
	        	$this.blur(function(){
	        		clearInterval(keepAtIt);
	        	});
	        }
	    });

	})(jQuery);

	$('#submit').click(function(){
		var cash = parseInt(noCommas($('#user-cash').text())),
			net = parseInt(noCommas($('.state .net span').text()));

		if (cash + net < 0) {
			$('.warning').remove();
			$(this).after('<h3 class="warning">If the budget remains where it is now, the state will go bankrupt. You need to cut funding to some cities, or find another way to come up with the cash.</h3>');
			return false;
		}
	});

	input.focus(function(){
		$this = $(this);
		$this.updateTotal();
		$this.addClass('f');
	});
});