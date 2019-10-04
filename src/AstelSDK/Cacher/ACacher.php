<?php

namespace AstelSDK\Cacher;

abstract class ACacher {
	
	protected $env_particle;
	
	public function uKey($key, $dataKey) {
		return $key . '_' . md5(print_r($data, true));
	}
}