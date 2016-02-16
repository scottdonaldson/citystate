var LocalStrategy = require('passport-local').Strategy,
	btoa = require('btoa'),
	decode = require('./helpers/decode');

function init(passport, db) {

	// Configure the local strategy for use by Passport.
	//
	// The local strategy require a `verify` function which receives the credentials
	// (`username` and `password`) submitted by the user.  The function must verify
	// that the password is correct and then invoke `cb` with a user object, which
	// will be set at `req.user` in route handlers after authentication.
	passport.use(new LocalStrategy(
	    function(username, attempt, cb) {
	    	db.getUser(username, function(data) {
				
				var password = data.password;
	            attempt = btoa(attempt);
	            
	            if ( password !== attempt ) { return cb(null, false); }
	            return cb(null, data);
	    	}, cb);
	    })
	);

	// Configure Passport authenticated session persistence.
	//
	// In order to restore authentication state across HTTP requests, Passport needs
	// to serialize users into and deserialize users out of the session.  The
	// typical implementation of this is as simple as supplying the user ID when
	// serializing, and querying the user record by ID from the database when
	// deserializing.
	passport.serializeUser(function(data, cb) {
		var id = data.id;
		cb(null, id);
	});

	passport.deserializeUser(function(id, cb) {
		db.getUser(id, function(user) {
			delete user.password;
			return cb(null, user);
		}, cb);
	});

	return passport;

}

module.exports = init;