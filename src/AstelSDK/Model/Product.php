<?php

namespace AstelSDK\Model;

use CakeUtility\Hash;

class Product extends SDKModel {
	
	const CONSUMER_TYPE_SMALL = 'SMALL';
	const CONSUMER_TYPE_MEDIUM = 'MEDIUM';
	const CONSUMER_TYPE_HEAVY = 'HEAVY';
	const CONSUMER_TYPE_HEAVYINT = 'HEAVYINT';
	
	protected $associated_instance_name = '\AstelSDK\API\Product';
	
	public function isType(array $product, $type) {
		if (null === $product || empty($product)) {
			return false;
		}
		if (strtoupper($type) === 'M' && Hash::get($product, 'is_mobile')) {
			return true;
		}
		if (strtoupper($type) === 'F' && Hash::get($product, 'is_fix')) {
			return true;
		}
		if (strtoupper($type) === 'I' && Hash::get($product, 'is_internet')) {
			return true;
		}
		if (strtoupper($type) === 'T' && Hash::get($product, 'is_tv')) {
			return true;
		}
		if (strtoupper($type) === 'H' && Hash::get($product, 'is_hardware')) {
			return true;
		}
		
		return false;
	}
	
	public function isMobileSolo(array $product) {
		if ($this->isType($product, 'M') && !$this->isType($product, 'F') && !$this->isType($product, 'I') && !$this->isType($product, 'T')) {
			return true;
		}
		
		return false;
	}
	
	public function isFixSolo($product) {
		if (!$this->isType($product, 'M') && $this->isType($product, 'F') && !$this->isType($product, 'I') && !$this->isType($product, 'T')) {
			return true;
		}
		
		return false;
	}
	
	public function isInternetSolo($product) {
		if (!$this->isType($product, 'M') && !$this->isType($product, 'F') && $this->isType($product, 'I') && !$this->isType($product, 'T')) {
			return true;
		}
		
		return false;
	}
	
	public function isTvSolo($product) {
		if (!$this->isType($product, 'M') && !$this->isType($product, 'F') && !$this->isType($product, 'I') && $this->isType($product, 'T')) {
			return true;
		}
		
		return false;
	}
	
	public function getBiggerUsageType($type = Product::CONSUMER_TYPE_SMALL) {
		switch ($type) {
			case Product::CONSUMER_TYPE_SMALL:
				return Product::CONSUMER_TYPE_MEDIUM;
			case Product::CONSUMER_TYPE_MEDIUM:
				return Product::CONSUMER_TYPE_HEAVY;
			case Product::CONSUMER_TYPE_HEAVY:
				return Product::CONSUMER_TYPE_HEAVYINT;
			case Product::CONSUMER_TYPE_HEAVYINT:
				return null;
		}
		
		return null;
	}
}