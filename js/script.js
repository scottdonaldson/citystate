// Note -- using $ as shortcut for Snap. Is this crazy? Maybe.
(function($){

	/* ----- Ability to remove alert ----- */

	function closeAlert() {
		$.select('#alert').remove();
	}
	$.select('.close-box').click(closeAlert);

	
	/* ----- Snapshot links ----- */

	var snapshots = $.selectAll('.snapshot');
	function retrieveSnapshot(e) {
		e.preventDefault();

		// Hide any alerts that are out there
		closeAlert();

		$.ajax(this.getAttribute('href'), function(data) {
			console.log(JSON.parse(data.response));
		});
	}
	for (var i = 0; i < snapshots.length; i++) {
		snapshots[i].click(retrieveSnapshot);
	}

})(Snap);