<?php

namespace AstelSDK\Model;

abstract class SDKModel extends SingletonAssociated {
	
	protected $isAssociatedInstance = true;
	protected $associated_instance_name = null;
	protected $associated_instance = null;
	protected $methodMap = [];
	
	/**
	 * SDKModel constructor.
	 *
	 * @param bool $isAssociatedInstance True, associate the $associated_instance_name to the current object, so
	 * you can call every methods of the associated object
	 *
	 * @see https://www.php.net/manual/en/language.oop5.magic.php#118617
	 */
	public function __construct($isAssociatedInstance = true) {
		if ($isAssociatedInstance && $this->associated_instance_name !== null) {
			// should be a singleton instance
			$this->associated_instance = call_user_func_array([$this->associated_instance_name, 'getInstance'], []);
			$this->mapApiMethods();
		}
		$this->isAssociatedInstance = $isAssociatedInstance;
	}
	
	private function mapApiMethods() {
		if ($this->associated_instance !== null) {
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