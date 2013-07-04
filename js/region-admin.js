jQuery(window).ready(function($){
	var value;
	$('.tile input').change(function(){
		$this = $(this);
		if ($this.is(':radio')) {
			value = $this.val();
			// show the tile switching terrain but keep the .tile class
			$this.closest('.tile').removeClass().addClass('tile ' + value);
		} else if ($this.is(':checkbox')) {
			// If checking, need a number input for resource value
			if ($this.is(':checked')) {
				// If there's already one (i.e. had been hidden and reshown),
				// switch from hidden to number
				if ($this.next('input').length > 0) {
					$this.next('input').show();
				// If there isn't one, add input to the DOM and set name
				} else {
					$this.after('<input type="number" name="' + $this.attr('id') + '">');
				}
			// If unchecking, hide the input and set to delete upon submitting
			} else {
				$this.next('input').hide().val('0');
			}
		}
	});

	var body = $('body');
	$('#overlays li').click(function(){
		$this = $(this);
		$this.css('color', '#fff').siblings().css('color', '#000');
		body.attr('data-overlay', $this.attr('data-overlay'));
	});
});