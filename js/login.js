jQuery(document).ready(function($){
	$('.login h1 a').text('City/State');

	var registerText = $('p.register');
	registerText.text('Sign up for an account here. Your username should be no more than 12 characters long, and can contain only letters and numbers.');

	$('#user_login').attr('maxlength', '12');


	/*! Work in progress...


	$('form').on('submit',function(){
		var special = [ ' ', '`', '~', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_', '=', '+', '[', '{', ']', '}', '\\', '|', ';', ':', '"', '<', '>', '/', '?'];
		$('#login_error').remove();
		registerText.after('<div id="login_error">ERROR: Your username can&apos;t contain any special characters.');
		return false;
	});

	*/

});