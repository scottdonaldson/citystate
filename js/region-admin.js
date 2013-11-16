window.addEventListener('load', function(){
	var value,
		input = document.querySelectorAll('.tile input');
	for (var i = 0; i < input.length; i++) {
		input[i].addEventListener('change', function(){
			var parentTile = closest(this, '.tile');
			parentTile.className = 'tile';
			parentTile.setAttribute('data-terrain', this.value);
		});
	}
});