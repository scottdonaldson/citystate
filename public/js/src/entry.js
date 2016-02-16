var $ = require('jquery'),
	body = $('body');

body.on('click', '#alert', function() {
	$(this).remove();
});

body.on('click', '.region rect', function() {
	var id = $(this).closest('.region').attr('data-id');
	window.location.href = '/region/' + id;
});