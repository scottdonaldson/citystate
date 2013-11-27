/* 
 *
 *  Set some GLOBAL CONSTANTS
 *
 *	BASE    : Base URL
 *  IS_PROD : false if development, true if production
 *	FB_URL	: URL for FireBase endpoint
 *
 */

var BASE = window.location.origin.indexOf('.com') > 0 ? window.location.origin : 'localhost/citystate',
	IS_PROD = BASE !== 'localhost/citystate' ? true : false,
	FB_URL = IS_PROD ? 'https://citystate.firebaseio.com' : 'https://citystate-dev.firebaseio.com';