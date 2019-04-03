<?php

namespace AstelSDK\Model;

abstract class SDKModel extends Singleton {
	
	protected $associated_instance_name = null;
	protected $associated_instance = null;
	protected $methodMap = [];
	
	public function __construct() {
		if ($this->associated_instance_name !== null) {
			// should be a singleton instance
			$this->associated_instance = call_user_func_array([$this->associated_instance_name, 'getInstance'], []);
			$this->mapApiMethods();
		}
	}
	
	private function mapApiMethods() {
		if ($this->associated_instance !== null) {
			$reflectionClass = new ReflectionClass(get_class($this->associated_instance));
			
			foreach ($reflectionClass->getMethods() as $m) {
				$this->methodMap[] = $m->name;
			}
		}
	}
	
	public function __call($method, $args) {
		if ($this->associated_instance !== null) {
			if (in_array($method, $this->methodMap)) {
				return call_user_func_array([$this->_instance, $method], $args);
			}
		}
		throw new Exception('Called function  "' . get_class($this) . '::' . $method . '" does not exist');
	}
}