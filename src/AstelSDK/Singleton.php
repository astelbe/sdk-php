<?php

namespace AstelSDK;

class Singleton {
	
	protected static $instances;
	
	protected function __construct() {
	}
	
	public static function getInstance() {
		$class = static::class;
		if (!isset(self::$instances[$class])) {
			self::$instances[$class] = new $class;
		}
		
		return self::$instances[$class];
	}
	
}