<?php

namespace AstelSDK\Model;

use AstelSDK\Utils\Singleton;

abstract class SingletonAssociated extends Singleton {
	
	/**
	 * Returns an instance without the associated instance
	 *
	 * @return mixed The instance of the requested Class.
	 */
	public static function getInstanceSimple() {
		$class = static::class;
		if (!isset(self::$instances[$class])) {
			self::$instances[$class] = new $class(false);
		}
		
		return self::$instances[$class];
	}
}