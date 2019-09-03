<?php

namespace AstelSDK\Model;

use CakeUtility\Hash;

class ProductCompare extends SDKModel {
	
	const ORDER_PRICE = 'price';
	const ORDER_QUALITY = 'quality';
	const ORDER_QUALITY_PRICE = 'qualityprice';
	const ORDER_DELAY = 'delay';
	
	protected $associated_instance_name = '\AstelSDK\API\ProductCompare';
	
	public function similarProductsSetParams(array $product) {
		$Product = Product::getInstance();
		if ($Product->isType($product, 'M')) {
			$isSmall = (int)$Product->isUsageType($product, 'M', Product::CONSUMER_TYPE_SMALL);
			$isMedium = (int)$Product->isUsageType($product, 'M', Product::CONSUMER_TYPE_MEDIUM);
			$isHeavy = (int)$Product->isUsageType($product, 'M', Product::CONSUMER_TYPE_HEAVY);
			$isHeavyInt = (int)$Product->isUsageType($product, 'M', Product::CONSUMER_TYPE_HEAVYINT);
			$this->paramMobile(true, $isSmall, $isMedium, $isHeavy, $isHeavyInt);
		}
		if ($Product->isType($product, 'F')) {
			if ($Product->isUsageType($product, 'F', Product::CONSUMER_TYPE_HEAVYINT)) {
				$this->paramFix(true, Product::CONSUMER_TYPE_HEAVYINT);
			} elseif ($Product->isUsageType($product, 'F', Product::CONSUMER_TYPE_HEAVY)) {
				$this->paramFix(true, Product::CONSUMER_TYPE_HEAVY);
			} elseif ($Product->isUsageType($product, 'F', Product::CONSUMER_TYPE_MEDIUM)) {
				$this->paramFix(true, Product::CONSUMER_TYPE_MEDIUM);
			}
		}
		if ($Product->isType($product, 'I')) {
			if ($Product->isUsageType($product, 'I', Product::CONSUMER_TYPE_HEAVYINT)) {
				$this->paramInternet(true, Product::CONSUMER_TYPE_HEAVYINT);
			} elseif ($Product->isUsageType($product, 'I', Product::CONSUMER_TYPE_HEAVY)) {
				$this->paramInternet(true, Product::CONSUMER_TYPE_HEAVY);
			} elseif ($Product->isUsageType($product, 'I', Product::CONSUMER_TYPE_MEDIUM)) {
				$this->paramInternet(true, Product::CONSUMER_TYPE_MEDIUM);
			}
		}
		if ($Product->isType($product, 'T')) {
			$this->paramTv(true);
		}
	}
	
	public function resultsExtractOnly1ProductResult(array $results, $paramFindProduct = []) {
		$Product = Product::getInstance();
		$extracted = [];
		foreach ($results as $result) {
			if ($result['number_products'] == 1) {
				$product_id = array_keys($result['product_ids'])[0];
				$thisProduct = [];
				$paramFindProduct = Hash::merge($paramFindProduct, ['id' => $product_id]);
				$thisProduct['product'] = $Product->find('first', $paramFindProduct);
				$thisProduct['result'] = $result;
				$extracted[] = $thisProduct;
			}
		}
		
		return $extracted;
	}
	
}