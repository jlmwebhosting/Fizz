<?php

require dirname(__FILE__).'/dependencies.php';
require dirname(__FILE__).'/../library/form.php';

class Fizz_Test extends PHPUnit_Framework_TestCase
{
	public function testSetData() {
		$errors = array();
		$values = array();
		
		$class = $this->getMockClass('Fizz\Form', array('set_errors', 'set_values'));
		$class::staticExpects($this->once())->method('set_errors')->with($errors);
		$class::staticExpects($this->once())->method('set_values')->with($values);
		
		$class::set_data($errors, $values);
	}
}
