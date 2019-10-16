<?php

namespace AstelSDK\Cacher;

abstract class ACacher {
	
	protected $env_particle;
	
	public function uKey($key, $dataKey) {
		$key = str_replace('\\', '_', $key);
		
		return $key . '_' . md5(print_r($dataKey, true));
	}
}