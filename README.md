Fizz
====

Fizz is a connector between Laravel&#39;s Form and Validator classes, providing 2 core pieces of functionality: form field value population, and error highlighting. It's goals were to bridge the gap between these two related libraries, and yet stay flexible to keep out of the developer's way when needed.

Installation
------------
Download the Fizz files and install into your application root/bundles directory. Then, in application/start.php, add the following:

	return array(
		'fizz' => array('auto' => true)
	);

This will load the Fizz library upon every request.

Usage
-----
Fizz currently wraps around Laravel's form library and adds some functionality. Form field population gets handled automagically, you don't need to worry about that. However, in the current implementation of Form, there is a disconnect between it and Validator. Some would see this as a bad thing, I actually think it's a strength. Nothing worse than a framework that tries to do everything for you.

To get error handling working, whenever you add some validation to your code, do the following:
	
	$post = Input::all();

	$rules = array(
		'email' => 'required|email',
		'password' => 'required'
	);

	$validation = Validator::make($post, $rules);
	Fizz\Form::set_validator($validation);

What this does is make the $validator object available to Fizz, and allows us to do some error checking. If any errors are found for a given form element, it will attach a "form-error" class to that field. This is the default however, and can be changed by (anywhere in your app), setting:

	Fizz\Form::set_error_class($error_class);

Optional
--------
If you would like to replace the Laravel Form library calls (recommended), you can do so by replacing the following in your application/config/application.php file:

	'Form' => 'Laravel\\Form',

With:

	'Form' => 'Fizz\\Form',

What this does, is basically make all Form requests in your markup, Fizz\Form requests (Otherwise you need to call Fizz\Form::.etc. everywhere in your HTML!)

One thing to note is that this does NOT remove the original Laravel Form calls. In fact, all it's doing is wrapping your call with some functionality to check for errors, field values.etc, then sending the call along to the Laravel\Form library.

Easy!
