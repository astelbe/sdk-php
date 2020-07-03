<?php

namespace AstelSDK\Cacher;

interface ICacher {
	
	public function add($key, $value);
	
	public function get($key, $notFoundValue = null);
	
	public function delete($key);
	
	public function keys($path = '');
	
	public function uKey($key, $dataKey);
}