/* ----- Remove alert ----- */

function closeAlert() {
	Snap.select('#alert').remove();
}
// Snap.select('.close-box').click(closeAlert);


/* ----- Show info box ------ */

function showInfobox(e, content) {
	var infobox = $('#infobox');
	infobox.innerHTML = content;
	infobox.style.display = 'block';
	infobox.style.left = e.x + 480 < window.outerWidth ? (e.x + 20) + 'px' : (e.x - 420) + 'px';
	infobox.style.top = ( e.y + window.scrollY - infobox.clientHeight / 2 ) + 'px';

	// Set global X and Y coordinates based on mouse click
	X = Math.floor(e.offsetX / TILE_WIDTH);
	Y = Math.floor(e.offsetY / TILE_WIDTH);
}

function hideInfobox() {
	var infobox = $('#infobox');
	infobox.style.display = 'none';
}
window.addEventListener('resize', hideInfobox);
window.addEventListener('keydown', function(e) {
	if (e.keyCode === 27) {
		hideInfobox();
	}
});

/* ----- Snapshot links ----- */

var snapshots = Snap.selectAll('.snapshot');
function retrieveSnapshot(e) {
	e.preventDefault();

	// Hide any alerts that are out there
	closeAlert();

	Snap.ajax(this.getAttribute('href'), function(data) {
		console.log(JSON.parse(data.response));
	});
}
for (var i = 0; i < snapshots.length; i++) {
	snapshots[i].click(retrieveSnapshot);
}
