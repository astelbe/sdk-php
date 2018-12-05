<?php

namespace AstelSDK;

use CakeUtility\Hash;

class Product extends QueryManager implements IApiConsumer {
	
	public $types = ['is_mobile', 'is_internet', 'is_tv', 'is_fix'];
	
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
		$url = '/product';
		$cond = [
			'is_visible' => 1,
		];
		if ($this->context->getIsPrivate()) {
			$cond['is_private'] = 1;
		} else {
			$cond['is_professionnal'] = 1;
		}
		$default_params = [
			'conditions' => $cond,
		];
		$params = Hash::merge($default_params, $params);
		$url = $this->addUrlParams($url, $params, true);
		$this->setUrl($url);
		
		return $this->exec();
	}
	
	protected function getFirst(array $params = []) {
		$id = Hash::get($params, 'conditions.id');
		if ($id === null || !is_numeric($id)) {
			return false;
		}
		$this->init();
		$url = '/product/';
		$url .= $id;
		$this->setUrl($url);
		
		return $this->exec(true);
	}
	
	public function transformResultArray(array $products) {
		$out = [];
		
		foreach ($products as $product) {
			$idProduct = Hash::get($product, 'id');
			$out[$idProduct] = $product;
		}
		
		return $out;
	}
	
	public function getMfitType(array $product) {
		$MFIT = '';
		if ($product['is_mobile']) {
			$MFIT .= 'M';
		}
		// Fixe
		if ($product['is_fix']) {
			$MFIT .= 'F';
		}
		// Internet
		if ($product['is_internet']) {
			$MFIT .= 'I';
		}
		// Tv
		if ($product['is_tv']) {
			$MFIT .= 'T';
		}
		
		return $MFIT;
	}
}