<?php

namespace AstelSDK\API;

use AstelSDK\QueryManager;
use CakeUtility\Hash;

/**
 * Class DiscountApi
 */
class Discount extends QueryManager implements IApiConsumer {
	
	public function find($type, array $params = []) {
		$cacheKey = md5($type . print_r($params, true));
		if (isset($this->cacheResults[$cacheKey])) {
			return $this->cacheResults[$cacheKey];
		}
		$result = false;
		if ($type === 'first') {
			$result = $this->getFirst($params);
			
		} elseif ($type === 'all') {
			$result = $this->getAll($params);
		}
		$this->cacheResults[$cacheKey] = $result;
		
		return $result;
	}
	
	protected function getAll(array $params = []) {
		$this->init();
		$url = 'v2_00/discount';
		$url = $this->addUrlParams($url, $params);
		$this->setUrl($url);
		
		return $this->exec(self::RETURN_MULTIPLE_ELEMENTS);
	}
	
	protected function getFirst(array $params = []) {
		$id = Hash::get($params, 'id');
		if ($id === null || !is_numeric($id)) {
			return false;
		}
		$this->init();
		$url = '/discount/';
		$url .= $id;
		$this->setUrl($url);
		
		return $this->exec(self::RETURN_SINGLE_ELEMENT);
	}
}