<?php

namespace AstelSDK\Cacher;

use CakeUtility\Hash;

class RedisCacher extends ACacher implements ICacher {
	
	protected $engine = null;
	
	public function __construct($params) {
		$host = Hash::get($params, 'host', '127.0.0.1');
		$port = Hash::get($params, 'port', '6379');
		$this->env_particle = Hash::get($params, 'env_particle', 'sdk_');
		
		$redis = new \Redis();
		if (!$redis->connect($host, $port)) {
			throw new RuntimeException ('Impossible to connect to the Redis caching server');
		}
		$this->engine = $redis;
	}
	
	public function add($key, $value, $ttl_seconds = 3600) {
		$value = json_encode($value);
		
		return $this->engine->set($this->env_particle . $key, $value, ['ex' => $ttl_seconds]);
	}
	
	public function get($key, $notFoundValue = null) {
		$value = $this->engine->get($this->env_particle . $key);
		if ($value === false) {
			return $notFoundValue;
		}
		
		return json_decode($value, true);
	}
	
	public function delete($key) {
		return false;
		// TODO: Implement delete() method.
	}
	
	public function keys($path = '') {
		return [];
		// TODO: Implement keys() method.
	}
}