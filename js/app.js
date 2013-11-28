/* 
 *
 *  Set some GLOBAL CONSTANTS
 *
 *	BASE 		: Base URL
 *  IS_PROD 	: false if development, true if production
 *	FB_URL		: URL for Firebase endpoint
 *	DATA		: Reference to the Firebase data
 *  LOGGED_IN	: pull from localStorage or false if not present
 *	USER		: returns user ID from local Storage, or false if not present
 *	VERSION 	: Release version
 *
 *	TILE_WIDTH	: size of tiles on map
 *
 */

var BASE 		= window.location.origin,
	IS_PROD 	= BASE.indexOf('.dev') > -1 ? false : true,
	FB_URL 		= IS_PROD ? 'https://citystate.firebaseio.com' : 'https://citystate-dev.firebaseio.com',
	DATA		= new Firebase(FB_URL),
	LOGGED_IN 	= localStorage.getItem('LOGGED_IN') || false,
	USER		= localStorage.getItem('USER') || false,
	VERSION 	= '0.0.7',

	TILE_WIDTH = 40;