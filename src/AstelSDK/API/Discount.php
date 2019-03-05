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
	
	public function retrieveAssociatedProductData($discounts = []) {
		$associatedProductIds = $this->getProductIds($discounts);
		
		$Product = Product::getInstance();
		$associatedProducts = $Product->find('all', [
					'brand_id' => BRAND_ID,
					'id' => $associatedProductIds,
			]
		);
		$discounts['products'] = $this->transformIdToReturnedArray($associatedProducts, 'id');
		$discounts = $this->clearUnvailableProduct($discounts);
		$discounts = $this->clearEmptyDiscounts($discounts);
		
		return $discounts;
	}
	
	private function getProductIds($discounts = []) {
		// Assign products
		$products = [];
		foreach ($discounts as $d => $discount) {
			if (isset($discount['products'])) {
				foreach ($discount['products'] as $k => $product) {
					$products[] = $product['product_id'];
				}
			}
		}
		
		return $products;
	}
	
	private function clearUnvailableProduct($discounts = []) {
		foreach ($discounts as $k => $discount) {
			if (is_numeric($k) && isset($discount['products'])) {
				foreach ($discount['products'] as $kTemp => $product) {
					if (!isset($discounts['products'][$product['product_id']])) {
						unset($discounts[$k]['products'][$kTemp]);
					}
				}
			}
			
		}
		
		return $discounts;
		
	}
	
	private function clearEmptyDiscounts($discounts = []) {
		foreach ($discounts as $k => $discount) {
			if (is_numeric($k)) {
				if (empty($discount['products'])) {
					unset($discounts[$k]);
				} else {
				}
			}
		}
		
		return $discounts;
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