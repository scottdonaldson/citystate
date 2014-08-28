/* ----- Remove alert ----- */

CS.remove = function(el) {
	el.parentNode.removeChild(el);
}
// Snap.select('.close-box').click(closeAlert);


/* ----- Show info box ------ */

CS.showInfobox = function(e, elem) {
	var infobox = CS('#infobox');

	// In case there had been anything in it, empty the infobox
	infobox.innerHTML = '';

	// Append the new element
	infobox.appendChild(elem);

	// Show and position the infobox
	infobox.style.display = 'block';
	infobox.style.left = e.x + 480 < window.outerWidth ? 
		(e.x + 30) + 'px' : 
		(e.x - infobox.clientWidth - 30) + 'px';
	infobox.style.top = ( e.y + window.scrollY - infobox.clientHeight / 2 ) + 'px';

	// Set global X and Y coordinates based on mouse click
	CS.X = Math.floor(e.offsetX / CS.TILE_WIDTH);
	CS.Y = Math.floor(e.offsetY / CS.TILE_WIDTH);
}

CS.hideInfobox = function() {
	var infobox = CS('#infobox');
	// Hide and empty the infobox
	infobox.style.display = 'none';
	infobox.innerHTML = '';
}
window.addEventListener('resize', function(){
	CS.hideInfobox();
});
window.addEventListener('keydown', function(e) {
	if (e.keyCode === 27) {
		CS.hideInfobox();
	}
});

/* ----- Snapshot links ----- */

var snapshots = CS('.snapshot');
function retrieveSnapshot(e) {
	e.preventDefault();

	// Hide any alerts that are out there
	closeAlert();

	Snap.ajax(this.href, function(data) {
		console.log(JSON.parse(data.response));
	});
}
for (var i = 0; i < snapshots.length; i++) {
	snapshots[i].addEventListener('click', retrieveSnapshot);
}