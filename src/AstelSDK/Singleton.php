<?php

namespace AstelSDK;

class Singleton {
	
	protected static $instances;
	
	public function __construct() {
	}
	
	public static function getInstance() {
		$class = static::class;
		if (!isset(self::$instances[$class])) {
			self::$instances[$class] = new $class;
		}
		
		return self::$instances[$class];
	}
	
	public static function reloadInstance() {
		$class = static::class;
		self::$instances[$class] = new $class;
		
		return self::$instances[$class];
	}
	
}