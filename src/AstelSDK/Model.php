<?php

namespace AstelSDK;

use CakeUtility\Hash;

/**
 * Class QueryManager
 *
 * @package AstelSDK
 */
abstract class Model extends Singleton {
	
	protected $context;
	protected $cacheResults = []; // To use only for single product
	protected $apiParticle = 'api';
	
	public function __construct() {
		$this->context = AstelContext::getInstance();
	}
	
	protected function isInCache() {
	
	}
	
	public function setApiParticle($particle) {
		$this->apiParticle = $particle;
	}
	
	/**
	 * @return APIQuery object
	 */
	public function newQuery() {
		$newQuery = new APIQuery($this->apiParticle);
		
		return $newQuery;
	}
	
	public function exists($id) {
		$is_exit = $this->find('first', ['conditions' => ['id' => $id]]);
		
		return $is_exit !== false && !empty($is_exit);
	}
	
	public function find($type, array $params = []) {
		$cacheKey = md5($type . print_r($params, true));
		if (isset($this->cacheResults[$cacheKey])) {
			return $this->cacheResults[$cacheKey];
		}
		if ($type === 'first') {
			$first = $this->getFirst($params);
			$this->cacheResults[$cacheKey] = $first;
			
			return $first;
		} elseif ($type === 'all') {
			$all = $this->getAll($params);
			$this->cacheResults[$cacheKey] = $all;
			
			return $all;
		}
		
		return false;
	}
	
	public function create(array $data = []) {
		return $this->createFirst($data);
	}
	
	public function transformIdToReturnedArray(array $array = [], $idName) {
		$out = [];
		foreach ($array as $a) {
			$out[Hash::get($a, $idName)] = $a;
		}
		
		return $out;
		
	}
	
	protected function log($message, $level = 'notice', $context = []) {
		return $this->context->log($message, $level, $context);
	}
	
}