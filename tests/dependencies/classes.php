<?php

namespace Laravel;

class Session {
	// Provides an interesting way for us to return 'something'
	public static function get($index) {
		return array($index => array($index));
	}
}

class Input {
	public static function get() {
		return array('name' => 'Something', 'age' => 18);
	}
	
	public static function old() {
		return array('name' => 'Old Something', 'age' => 17, 'csrf_token' => 'aksdhifasdjkaskdfj');
	}
}

class Config {
	private static $calls = 0;
	
	public static function get($item) {
		$return = $item . ' ' . self::$calls;
		self::$calls++;
		
		return $return;
	}
}

class Form {
	
}