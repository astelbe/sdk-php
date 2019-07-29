<?php

namespace AstelSDK\Model;

use CakeUtility\Hash;
use CakeUtility\Set;

class Product extends SDKModel {
	
	const CONSUMER_TYPE_SMALL = 'SMALL';
	const CONSUMER_TYPE_MEDIUM = 'MEDIUM';
	const CONSUMER_TYPE_HEAVY = 'HEAVY';
	const CONSUMER_TYPE_HEAVYINT = 'HEAVYINT';
	const AVAILABILITY_ZONE_ALL_COUNTRY = ['BE' => 10];
	
	protected $associated_instance_name = '\AstelSDK\API\Product';
	
	public function isProductAvailableAllCountry(array $product) {
		if (!empty($product)) {
			$productAvailabilityZone = Hash::get($product, 'availability_zone_id');
			
			return $productAvailabilityZone === self::AVAILABILITY_ZONE_ALL_COUNTRY['BE'];
		}
		
		return false;
	}
	
	public function isAllProductsAvailableAllCountry(array $products) {
		$nbrProducts = count($products);
		$isAvailable = 0;
		foreach ($products as $product) {
			if ($this->isProductAvailableAllCountry($product)) {
				$isAvailable++;
			}
		}
		
		return $isAvailable === $nbrProducts;
	}
	
	public function isProductAvailableTmpProcessing($product_id, $searchTxt) {
		$availablePostalCodes = $this->isAvailableSearch($product_id, $searchTxt);
		// First postcode found should be available for sale
		if (!empty($availablePostalCodes)) {
			$isAvailable = true;
			foreach ($availablePostalCodes as $pc) {
				$isAvPc = Hash::get($pc, 'is_available');
				$isAvailable = $isAvailable && $isAvPc;
				// Get only the first one
				break;
			}
			
			return $isAvailable;
		}
		
		return false;
	}
	
	public function getProductNameById($id, $language) {
		if (isset($id) && $id != '' && is_numeric($id)) {
			$product = $this->find('first', ['id' => $id]);
			
			return Hash::get($product, 'name.' . strtoupper($language), '');
		}
		
		return '';
	}
	
	public function productSelectListWithID() {
		$prodDB = $this->findAll([]);
		
		return $this->productArrayToSelectableList($prodDB);
	}
	
	/**
	 * @param $prodDB
	 *
	 * @return array
	 */
	private function productArrayToSelectableList($prodDB) {
		$extractedList = Set::combine($prodDB, '{n}.id', '{n}.name.FR', '{n}.brand_name');
		$outList = [];
		foreach ($extractedList as $brand => $products) {
			foreach ($products as $productID => $product_name) {
				$outList[$productID] = $brand . ' ' . $product_name . ' - ' . $productID;
			}
		}
		
		// Order by product name
		asort($outList);
		
		return $outList;
	}
	
	public function productVariableSelectListWithID() {
		$prodDB = $this->findAll(['is_hardware' => 1, 'is_variable' => 1]);
		
		return $this->productArrayToSelectableList($prodDB);
	}
	
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
	
	public function getMfitType(array $product) {
		$MFIT = '';
		if ($this->isType($product, 'M')) {
			$MFIT .= 'M';
		}
		if ($this->isType($product, 'F')) {
			$MFIT .= 'F';
		}
		if ($this->isType($product, 'I')) {
			$MFIT .= 'I';
		}
		if ($this->isType($product, 'T')) {
			$MFIT .= 'T';
		}
		
		return $MFIT;
	}
	
	/**
	 * @param array $product
	 *
	 * @return bool whether the telecom pack has a fixed part
	 */
	public function hasProductFixPackPlayPart(array $product) {
		return $this->isType($product, 'F') || $this->isType($product, 'I') || $this->isType($product, 'T');
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
	
	/**
	 * @param array $product . Required to give product with embeded subscription_periods/discounts
	 */
	public function countValidDiscounts(array $product) {
		$subscription_periods = Hash::get($product, 'subscription_periods', []);
		$countValidDiscounts = 0;
		foreach ($subscription_periods as $subscription_period) {
			$discounts = Hash::get($subscription_period, 'discounts', []);
			foreach ($discounts as $discount) {
				if ($discount['is_active']) {
					$countValidDiscounts++;
				}
			}
		}
		
		return $countValidDiscounts;
	}
	
	public function isUsageType(array $product, $play, $usage) {
		$getPath = 'play_description.';
		if ($play === 'M') {
			$getPath .= 'mobile.consumer_caller_profile';
		} elseif ($play === 'F') {
			$getPath .= 'fix.consumer_caller_profile';
		} elseif ($play === 'I') {
			$getPath .= 'internet.consumer_profile';
		}
		$usageArray = Hash::get($product, $getPath, []);
		
		return in_array($usage, $usageArray);
	}
	
	public function isFeatured(array $product) {
		return Hash::get($product, 'is_featured', false);
	}
}