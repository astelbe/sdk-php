<?php

namespace AstelSDK\Utils;

use AstelSDK\Utils\Singleton;

abstract class SingletonAssociated extends Singleton {
	
	/**
	 * Returns an instance with the given associated
	 *
	 * @return mixed The instance of the requested Class.
	 */
	public static function getInstanceAssociated($associated_instance_object = null) {
		$class = static::class;
		if (!isset(self::$instances[$class])) {
			self::$instances[$class] = new $class($associated_instance_object);
		}
		
		return self::$instances[$class];
	}
	
	protected $isAssociatedInstance = true;
	protected $associated_instance_name = null;
	protected $associated_instance = null;
	protected $methodMap = [];
	
	/**
	 * @see https://www.php.net/manual/en/language.oop5.magic.php#118617
	 */
	public function __construct($associated_instance = null) {
		if ($associated_instance !== null) {
			$this->associated_instance_name = get_class($associated_instance);
			$this->associated_instance = $associated_instance;
		} else {
			if ($this->associated_instance_name !== null) {
				$this->isAssociatedInstance = true;
				// should be a singleton instance
				$this->associated_instance = call_user_func_array([$this->associated_instance_name, 'getInstance'], []);
			}
		}
		$this->mapApiMethods();
	}
	
	protected function mapApiMethods() {
		if ($this->associated_instance !== null && is_object($this->associated_instance)) {
			$reflectionClass = new \ReflectionClass(get_class($this->associated_instance));
			
			foreach ($reflectionClass->getMethods() as $m) {
				$this->methodMap[] = $m->name;
			}
		}
	}
	
	/**
	 * Allows to call any method of the associated object
	 *
	 * @param $method
	 * @param $args
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function __call($method, $args) {
		if ($this->isAssociatedInstance) {
			if ($this->associated_instance !== null) {
				if (in_array($method, $this->methodMap)) {
					return call_user_func_array([$this->associated_instance, $method], $args);
				}
			}
			throw new \Exception('Called function  "' . get_class($this) . '::' . $method . '" does not exist');
		}
	}
}