/* 
 *	First, the global CS object.
 *  Like jQuery, we'll use it as a function and an object.
 *
 *	If passed a selector, it will return the matched elements
 *	via document.querySelectorAll or document.getElementById.
 *	If these aren't found, just return the string.
 *	If passed an object, it just returns the object.
 *
 */

var CS = function(selector) {
	if (typeof selector === 'string') {
		var returned = (selector.split(' ').length === 1 && selector.slice(0, 1) === '#') ?
			document.getElementById( selector.slice(1) ) :
			document.querySelectorAll(selector);

		return (returned) ? returned : selector;

	// taken from jQuery's .isNumeric() method -- if a number, return as string
	} else {
		return selector;
	}
}

/*
 *  We'll also store our global constants in CS.
 *
 *	BASE 		: Base URL
 *  IS_PROD 	: false if development, true if production
 *	FB_URL		: URL for Firebase endpoint
 *	DATA		: Reference to the Firebase data
 *  LOGGED_IN	: pull from localStorage or false if not present
 *	USER		: returns user ID from local Storage, or false if not present
 *  SLUG		: populated based on what city/region user is viewing
 *	X, Y		: generate these conditionally depending on clicks on the map
 *	VERSION 	: Release version
 *
 *	TILE_WIDTH	: size of tiles on map
 *
 *  STRUCTURES  : all the structures available to build -- updated as the last step in this file 
 */

CS.BASE 		= location.origin,
CS.IS_PROD 		= CS.BASE.indexOf('.dev') > -1 ? false : true,
CS.FB_URL 		= CS.IS_PROD ? 'https://citystate.firebaseio.com' : 'https://citystate-dev.firebaseio.com',
CS.DATA 		= new Firebase(CS.FB_URL),
CS.LOGGED_IN 	= localStorage.getItem('LOGGED_IN') || false,
CS.USER 		= localStorage.getItem('USER') || false,
CS.SLUG 		= '',
CS.X 			= '', 
CS.Y 			= '',
CS.VERSION 	= '0.0.7',
CS.TILE_WIDTH 	= 40;

CS.DATA.once('value', function(data){
	// Update the global STRUCTURES object
	CS.STRUCTURES = {};
	for (var structure in data.child('structures').val()){
		CS.STRUCTURES[structure] = data.child('structures').val()[structure];
	}
});	