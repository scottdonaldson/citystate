var Unit = function(args) {
	if (!!args) { args = {}; }
}
Unit.prototype = new Snap();

var Tile = function(args) {
	if (!!args) { args = {}; }
	this.height = 60;
	this.width = 60;
	this.fill = args.fill || '#999';
	this.x = args.x > 0 ? args.x : 0;
	this.y = args.y > 0 ? args.y : 0;
}
Tile.prototype = new Snap();
console.log(new Unit());

function show_region_map() {
	console.log('showing region');
}