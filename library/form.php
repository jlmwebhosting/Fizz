<?php
/**
 * Fizz acts as a connector between Laravel's Form methods, and it's Validation
 * class, by setting some conventions by which the validators should be used, and making it's
 * data and errors accessible to the view.
 * 
 * All the form methods that Fizz provides are wrappers of the methods available
 * to Laravel\Form, the only difference being that Fizz will check for errors and then
 * add error classes to the elements that have failed validation. This allows users to easily
 * use a standard approach for handling both Forms, and Validation.
 *
 * @author Kirk Bushell
 * @date 20th April 2012
 */
namespace Fizz;

class Form
{
	/**
	 * Stores access to the validation class. When rendering
	 * form elements, we access this regularly to see if any
	 * error messages exist for a given field.
	 *
	 * @param \Laravel\Validator
	 */
	private static $_validator;

	/**
	 * The error class to be added to form fields if the 
	 * provided field has failed validation.
	 *
	 * @param string
	 */
	private static $_error_class = 'form-error';

	/**
	 * Stores the Validator object that we'll be using
	 * to check for error messages and the like.
	 * 
	 * @param Validator $validator
	 * @return void
	 */
	public static function set_validator(\Laravel\Validator $validator)
	{
		self::$_validator = $validator;
	}

	/**
	 * Sets the error class to be used for form elements that
	 * have failed validation.
	 *
	 * @param string $error_class
	 */
	public static function set_error_class($error_class) {
		self::$_error_class = $error_class;
	}

	/**
	 * A little magic. Checks for the method being called and then does the
	 * validation check for the given field. If the method called cannot match
	 * any method available on the class, it then passes the responsbility
	 * to the Laravel\Form class. In this sense it has a sort of pseudo-hierarchical
	 * aspect, but do not confuse it with any form of actual inheritance.
	 *
	 * @param string $method
	 * @param array $arguments
	 */
	public static function __callStatic($method, $arguments) 
	{
		// not all input methods need to be validated
		$valid_input_methods = array(
			'label', 'text', 'password', 'search', 'email',
			'telephone', 'url', 'number', 'date', 'file', 'textarea', 'select',
			'checkbox', 'radio', 'image'
		);

		// if the desired method is a form field of sorts, let's do some magic
		if (in_array($method, $valid_input_methods)) {
			$name = $arguments[0];
			$attr_key = count($arguments)-1;
			
			// check to see if it's valid, and update $attributes array if necessary
			$arguments[$attr_key] = self::_check($name, $arguments[$attr_key]);
			$arguments = self::_handle_value($method, $arguments);
		}

		// now call the original method that it was after. The reaso why we don't do a method
		// check, is because Laravel's Form class will handle the Form macros if they've been setup,
		// which we want to stay away from ;)
		return call_user_func_array(array('Laravel\Form', $method), $arguments);
	}

	/**
	 * Using the input class, the method, and the provided arguments, looks to see
	 * if a value is already present for the selected form element. If it is, it
	 * will correctly choose the right action (populate the value, check the box,
	 * select the appropriate option.etc.)
	 *
	 * @param string $method
	 * @param array $arguments
	 */
	private static function _handle_value($method, $arguments)
	{
		$set_value = false;
		$possible_value = \Laravel\Input::get($arguments[0]);

		if ($possible_value or $possible_value === 0) {
			$set_value = true;
		}

		switch ($method)
		{
			case 'text':
			case 'textarea':
			case 'email':
			case 'hidden':
			case 'search':
			case 'date':
			case 'number':
			case 'url':
			case 'tel':
				if ($set_value) $arguments[1] = $possible_value;
				break;
			case 'select':
				if ($set_value) $arguments[2] = $possible_value;
				break;
			case 'checkbox':
			case 'radio':
				if ($set_value) $arguments[2] = true;
		}

		return $arguments;
	}

	/**
	 * Checks to see if a given element has failed validation. If it has, it
	 * will append the error class to the element's class attribute.
	 *
	 * @param string $field
	 * @param array $attributes
	 */
	private static function _check($field, $attributes)
	{
		if (!is_array($attributes)) (array) $attributes;
		
		if (self::_invalid($field))
		{
			$attributes['class'] = (!isset($attributes['class'])) ? self::$_error_class : $attributes['class'].' '.self::$_error_class;
		}

		return $attributes;
	}

	/**
	 * Checks to see whether a given field is invalid
	 *
	 * @param string $field
	 * @return boolean true if invalid, false otherwise
	 */
	private static function _invalid($field)
	{
		return (self::$_validator && isset(self::$_validator->errors->messages[$field]));
	}
}
