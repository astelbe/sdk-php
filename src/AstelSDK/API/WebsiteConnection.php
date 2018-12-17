<?php

namespace AstelSDK\API;

use AstelSDK\QueryManager;
use CakeUtility\Hash;

class WebsiteConnection extends QueryManager implements IApiConsumer {
	
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
		global $sLang;
		$uniqueVisitorKey = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
		$default_params = [
			'conditions' => [
				'unique_visitor_key' => $uniqueVisitorKey,
				'language' => $sLang,
			],
		];
		$params = Hash::merge($default_params, $params);
		
		$this->init();
		$url = 'v2_00/website_connection/';
		$url = $this->addUrlParams($url, $params, true);
		$this->setUrl($url);
		
		return $this->exec(self::RETURN_MULTIPLE_ELEMENTS);
	}
}
