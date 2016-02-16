var $ = require('jquery');

$('body').on('click', '#alert', function() {
	$(this).remove();
});