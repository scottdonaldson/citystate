/* ----- Remove alert ----- */

function closeAlert() {
	Snap.select('#alert').remove();
}
// Snap.select('.close-box').click(closeAlert);


/* ----- Show info box ------ */

var isAdmin = document.body.classList.contains('admin-bar') ? 28 : 0;
function showInfobox(e, content) {
	var infobox = document.getElementById('infobox');
	infobox.innerHTML = content;
	infobox.style.display = 'block';
	infobox.style.left = e.x + 480 < window.outerWidth ? (e.x + 20) + 'px' : (e.x - 420) + 'px';
	infobox.style.top = (e.y + window.scrollY - infobox.clientHeight / 2 - isAdmin) + 'px';
}

function hideInfobox() {
	var infobox = document.getElementById('infobox');
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
