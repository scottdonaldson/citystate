var Structure = function() {
	this.width = 60;
	this.height = 60;
}
Structure.prototype = new Snap();

var Neighborhood = function(){

}

var structures = [Neighborhood, Park];

// Set prototype of these structures to the master Structure
for (var i = 0; i < structures.length; i++) {
	structures[i].prototype = new Structure();
}