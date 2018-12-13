<?php

namespace AstelSDK\API;

use AstelSDK\QueryManager;
use CakeUtility\Hash;

class Partner extends QueryManager implements IApiConsumer {
	
	public function find($type, array $params = []) {
		$cacheKey = md5(print_r($params, true));
		if (isset($this->cacheResults[$cacheKey])) {
			return $this->cacheResults[$cacheKey];
		}
		$result = false;
		if ($type === 'first' || $type === 'all') {
			$result = $this->getFirst($params);
		}
		$this->cacheResults[$cacheKey] = $result;
		
		return $result;
	}
	
	protected function getFirst(array $params = []) {
		$default_params = [
			'contains' => ['CallCenter', 'LastOrderedProducts'],
		];
		$params = Hash::merge($default_params, $params);
		
		$this->init();
		$url = 'v2_00/partner/';
		$url = $this->addUrlParams($url, $params, true);
		$this->setUrl($url);
		
		return $this->exec(self::RETURN_SINGLE_ELEMENT);
	}
}
