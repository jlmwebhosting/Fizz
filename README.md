Fizz
====

Fizz is a connector between Laravel&#39;s Form and Validator classes, providing 2 core pieces of functionality: form field value population, and error highlighting.

Installation
------------
Download the Fizz files and install into your application root/bundles directory. Then, in application/start.php, add the following:

	return array(
		'fizz' => array('auto' => true)
	);

This will load the Fizz library upon every request.

Optional
--------
If you would like to replace the Laravel Form library, you can do so by replacing the following in your application/config/application.php file:

	'Form' => 'Laravel\\Form',

With:

	'Form' => 'Fizz\\Form',

What this does, is basically make all Form requests in your markup, Fizz\Form requests (Otherwise you need to Use Fizz\Form everywhere in your HTML. One thing to note is that this does NOT remove the original Laravel Form calls. In fact, all it's doing is wrapping your call with some functionality to check for errors, field values.etc, then sending the call along to the Laravel\Form library.

Easy!
