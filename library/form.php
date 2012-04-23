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
	 * Does the meaty work, checking for existing values, field
	 * highlighting.etc.
	 *
	 * @return array First element is the value for the element, 2nd is the modified attributes array
	 */
	private static function _fizzle($name, $value, $attributes) {
		$value      = self::_handle_value($name, $value);
		$attributes = self::_check($name, $attributes);

		return array($value, $attributes);
	}

	/**
	 * Text field
	 */
	public static function text($name, $value = null, $attributes = array())
	{
		list($value, $attributes) = self::_fizzle($name, $value, $attributes);
		return \Laravel\Form::text($name, $value, $attributes);
	}

	/**
	 * Password field
	 */
	public static function password($name, $attributes = array())
	{
		list($value, $attributes) = self::_fizzle($name, '', $attributes);
		return \Laravel\Form::password($name, $attributes);
	}

	/**
	 * Hidden field
	 */
	public static function hidden($name, $value = null, $attributes = array())
	{
		$value = self::_handle_value($name, $value);
		return \Laravel\Form::hidden($name, $value, $attributes);
	}

	/**
	 * Search field
	 */
	public static function search($name, $value = null, $attributes = array())
	{
		list($value, $attributes) = self::_fizzle($name, $value, $attributes);
		return \Laravel\Form::search($name, $value, $attributes);
	}

	/**
	 * Email field
	 */
	public static function email($name, $value = null, $attributes = array())
	{
		list($value, $attributes) = self::_fizzle($name, $value, $attributes);
		return \Laravel\Form::email($name, $value, $attributes);
	}

	/**
	 * Telephone field
	 */
	public static function telephone($name, $value = null, $attributes = array())
	{
		list($value, $attributes) = self::_fizzle($name, $value, $attributes);
		return \Laravel\Form::telephone($name, $value, $attributes);
	}

	/**
	 * URL field
	 */
	public static function url($name, $value = null, $attributes = array())
	{
		list($value, $attributes) = self::_fizzle($name, $value, $attributes);
		return \Laravel\Form::url($name, $value, $attributes);
	}

	/**
	 * Number field
	 */
	public static function number($name, $value = null, $attributes = array())
	{
		list($value, $attributes) = self::_fizzle($name, $value, $attributes);
		return \Laravel\Form::number($name, $value, $attributes);
	}

	/**
	 * Date field
	 */
	public static function date($name, $value = null, $attributes = array())
	{
		list($value, $attributes) = self::_fizzle($name, $value, $attributes);
		return \Laravel\Form::date($name, $value, $attributes);
	}

	/**
	 * Textarea field
	 */
	public static function textarea($name, $value = null, $attributes = array())
	{
		list($value, $attributes) = self::_fizzle($name, $value, $attributes);
		return \Laravel\Form::textarea($name, $value, $attributes);
	}

	/**
	 * Select field
	 */
	public static function select($name, $options = array(), $selected = null, $attributes = array())
	{
		list($value, $attributes) = self::_fizzle($name, $selected, $attributes);
		return \Laravel\Form::select($name, $options, $value, $attributes);
	}

	/**
	 * Checkbox field
	 */
	public static function checkbox($name, $value = 1, $checked = false, $attributes = array())
	{
		$set_value = \Laravel\Input::get($name);
		if ($set_value) {
			$checked = true;
		}

		list($value, $attributes) = self::_fizzle($name, $value, $attributes);
		return \Laravel\Form::checkbox($name, $value, $checked, $attributes);
	}

	/**
	 * Radio field
	 */
	public static function radio($name, $value = null, $checked = false, $attributes = array())
	{
		$set_value = \Laravel\Input::get($name);
		if ($set_value == $value) {
			$checked = true;
		}

		$fizzle_check = self::_fizzle($name, $value, $attributes);
		$attributes = array_pop($fizzle_check);

		return \Laravel\Form::radio($name, $value, $checked, $attributes);
	}

	/**
	 * Any method that does not exist on this class should be immediately
	 * sent to Laravel's form class, so as to continue support of form macros.
	 */
	public static function __callStatic($method, $arguments) {
		return call_user_func_array('\\Laravel\\Form::' . $method, $arguments);
	}

	/**
	 * Using the input class, and the field name, looks to see
	 * if a value is already present for the selected form element. If it is, it
	 * will correctly choose the right action (populate the value, check the box,
	 * select the appropriate option.etc.)
	 *
	 * @param string $field
	 */
	private static function _handle_value($field, $default)
	{
		$set_value = false;
		$possible_value = \Laravel\Input::get($field);

		if ($possible_value or $possible_value === 0) {
			return $possible_value;
		}
		else {
			return $default;
		}
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
		if (self::$_validator)
		{
			// check to see if this field has a confirmation issue
			if (strpos($field, 'confirmation')) {
				$field_without_confirm = str_replace('_confirmation', '', $field);
				if (isset(self::$_validator->errors->messages[$field_without_confirm])) {
					// the field without the confirmation has an error, so we have to assume this one does as well
					return true;
				}
			}
		
			return (self::$_validator->invalid() && isset(self::$_validator->errors->messages[$field]));
		}
	}
}
