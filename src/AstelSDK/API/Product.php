<?php

namespace AstelSDK\API;

use AstelSDK\APIModel;
use CakeUtility\Hash;

class Product extends APIModel implements IApiConsumer {
	
	public $types = ['is_mobile', 'is_internet', 'is_tv', 'is_fix'];
	
	protected function getAll(array $params = []) {
		$query = $this->newQuery();
		$query->setUrl('v2_00/product');
		$default_params = [
			'is_visible' => 1,
		];
		if ($this->context->getIsPrivate()) {
			$default_params['is_private'] = 1;
		} else {
			$default_params['is_professionnal'] = 1;
		}
		$params = Hash::merge($default_params, $params);
		$query->addGETParams($params);
		
		return $query->exec();
	}
	
	protected function getFirst(array $params = []) {
		$id = Hash::get($params, 'conditions.id');
		if ($id === null || !is_numeric($id)) {
			return false;
		}
		unset($params['id']);
		$query = $this->newQuery();
		$query->setUrl('v2_00/product/' . $id);
		$query->addGETParams($params);
		
		return $query->exec();
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