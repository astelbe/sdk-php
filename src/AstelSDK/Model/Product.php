<?php

namespace AstelSDK\Model;

use AstelSDK\Utils\TypeTransform;
use CakeUtility\Hash;
use CakeUtility\Set;


class Product extends SDKModel {
	
	const CONSUMER_TYPE_SMALL = 'SMALL';
	const CONSUMER_TYPE_MEDIUM = 'MEDIUM';
	const CONSUMER_TYPE_HEAVY = 'HEAVY';
	const CONSUMER_TYPE_HEAVYINT = 'HEAVYINT';
	const CONSUMER_TYPE_ORDER = [
		self::CONSUMER_TYPE_SMALL => 0,
		self::CONSUMER_TYPE_MEDIUM => 1,
		self::CONSUMER_TYPE_HEAVY => 2,
		self::CONSUMER_TYPE_HEAVYINT => 3,
	];
	const DEFAULT_MOBILE_USAGE = [
		'mobile_small_qt' => 0,
		'mobile_regular_qt' => 0,
		'mobile_heavy_qt' => 0,
		'mobile_heavy_int_qt' => 0,
	];
	const AVAILABILITY_ZONE_ALL_COUNTRY = ['BE' => 10];
	
	protected $associated_instance_name = '\AstelSDK\API\Product';
	
	public function isProductAvailableAllCountry($product) {
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
	
	public function productSelectListWithID($conditions = []) {
		$prodDB = $this->findAll($conditions);
		
		return $this->productArrayToSelectableList($prodDB);
	}
	
	/**
	 * @param $prodDB
	 *
	 * @return array
	 */
	private function productArrayToSelectableList($prodDB) {
		$outList = [];
		if (!empty($prodDB)) {
			$extractedList = Set::combine($prodDB, '{n}.id', '{n}.name.FR', '{n}.brand_name');
			foreach ($extractedList as $brand => $products) {
				foreach ($products as $productID => $product_name) {
					$outList[$productID] = $brand . ' ' . $product_name . ' - ' . $productID;
				}
			}
			// Order by product name
			asort($outList);
		}
		
		return $outList;
	}
	
	public function productVariableSelectListWithID() {
		$prodDB = $this->findAll(['is_hardware' => 1, 'is_variable' => 1]);
		
		return $this->productArrayToSelectableList($prodDB);
	}
	
	public function isType($product, $type) {
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
	
	public function getMfitType($product) {
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
		if ($this->isType($product, 'H')) {
			$MFIT .= 'H';
		}
		
		return $MFIT;
	}
	
	/**
	 * @param array $product
	 *
	 * @return bool whether the telecom pack has a fixed part
	 */
	public function hasProductFixPackPlayPart($product) {
		return $this->isType($product, 'F') || $this->isType($product, 'I') || $this->isType($product, 'T');
	}
	
	public function isMobileSolo($product) {
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
	public function countValidDiscounts($product) {
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
	
	public function isUsageType($product, $play, $usage) {
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
	
	public function getOptionRelationsGroupedByType($product, $sort = []) {
		$options = Hash::get($product, 'option_relations', []);
		$group = [];
		foreach ($options as $option) {
			if (is_array($option['option']) && !empty($option['option'])) {
				$group[Hash::get($option, 'option.type')][] = $option;
			}
		}
		if (!empty($sort)) {
			foreach ($group as $play => $optionsPlay) {
				$optionsPlay = TypeTransform::sortTwoLevel($optionsPlay, $sort['first'], $sort['second'], $sort['first_dir'], $sort['second_dir']);
				$group[$play] = $optionsPlay;
			}
		}
		
		return $group;
	}
	
	public function getOptionGroupsGroupedByType($product, $sort = []) {
		$options = Hash::get($product, 'option_group_relations', []);
		$group = [];
		foreach ($options as $option) {
			if (is_array($option['option_group']) && !empty($option['option_group'])) {
				if (is_array(Hash::get($option, 'option_group.options')) && !empty(Hash::get($option, 'option_group.options'))) {
					$group[Hash::get($option, 'option_group.type')][] = $option;
				}
			}
			
		}
		if (!empty($sort)) {
			foreach ($group as $play => $optionGroupsPlay) {
				foreach ($optionGroupsPlay as $tmpID => $groupPlay) {
					$optionGroupPlay = $groupPlay['option_group']['options'];
					$optionGroupPlay = TypeTransform::sortTwoLevel($optionGroupPlay, $sort['first'], $sort['second'], $sort['first_dir'], $sort['second_dir']);
					$group[$play][$tmpID]['option_group']['options'] = $optionGroupPlay;
				}
				
			}
		}
		
		return $group;
	}
	
	public function getBiggestUsageInArray($usages) {
		$biggest = null;
		
		foreach ($usages as $tmp => $usage) {
			if ($biggest === null) {
				$biggest = $usage;
				continue;
			}
			if ($usage === Product::CONSUMER_TYPE_HEAVYINT) {
				$biggest = Product::CONSUMER_TYPE_HEAVYINT;
				break;
			}
			if (self::CONSUMER_TYPE_ORDER[$usage] > self::CONSUMER_TYPE_ORDER[$biggest]) {
				$biggest = $usage;
			}
		}
		
		return $biggest;
	}
	
	/**
	 * @param array $productData Need _embed "play_description" data
	 * @param array $_GET_params
	 *
	 * @return array
	 */
	public function determineComparatorGetParamsFromUsage($productData = [], $_GET_params = []) {
		$_GET_params['mobile'] = 0;
		if ($this->isType($productData, 'M')) {
			$_GET_params['mobile'] = 1;
			$profile = Hash::get($productData, 'play_description.mobile.consumer_caller_profile');
			if ($profile !== null) {
				$biggestUsage = $this->getBiggestUsageInArray($profile);
				if ($biggestUsage !== null) {
					$_GET_params = array_merge(self::DEFAULT_MOBILE_USAGE, $_GET_params);
					switch ($biggestUsage) {
						case self::CONSUMER_TYPE_SMALL:
							$_GET_params['mobile_small_qt'] = $_GET_params['mobile_small_qt'] + 1;
							break;
						case self::CONSUMER_TYPE_MEDIUM:
							$_GET_params['mobile_regular_qt'] = $_GET_params['mobile_regular_qt'] + 1;
							break;
						case self::CONSUMER_TYPE_HEAVY:
							$_GET_params['mobile_heavy_qt'] = $_GET_params['mobile_heavy_qt'] + 1;
							break;
						case self::CONSUMER_TYPE_HEAVYINT:
							$_GET_params['mobile_heavy_int_qt'] = $_GET_params['mobile_heavy_int_qt'] + 1;
							break;
					}
				}
			}
		}
		$_GET_params['fixe'] = 0;
		if ($this->isType($productData, 'F')) {
			$_GET_params['fixe'] = 1;
			$profile = Hash::get($productData, 'play_description.fix.consumer_caller_profile');
			if ($profile !== null) {
				$_GET_params['fix_usage'] = $this->getBiggestUsageInArray($profile);
			}
		}
		$_GET_params['internet'] = 0;
		if ($this->isType($productData, 'I')) {
			$_GET_params['internet'] = 1;
			$profile = Hash::get($productData, 'play_description.internet.consumer_profile');
			if ($profile !== null) {
				$_GET_params['internet_usage'] = $this->getBiggestUsageInArray($profile);
			}
		}
		$_GET_params['tv'] = 0;
		if ($this->isType($productData, 'T')) {
			$_GET_params['tv'] = 1;
		}
		
		return $_GET_params;
	}
	
	/*
	 * Used to determine get comparator param for a whole caddie. Each product data pass via  this->determineComparatorGetParamsFromUsage
	 * then results are merged using this function
	 */
	public function determineComparatorGetParamsAddition($get1, $get2) {
		$get1 = Hash::merge(self::DEFAULT_MOBILE_USAGE, $get1);
		$get2 = Hash::merge(self::DEFAULT_MOBILE_USAGE, $get2);
		$get1['mobile'] += $get2['mobile'];
		if ($get1['mobile'] > 1) {
			$get1['mobile'] = 1;
		}
		$get1['fixe'] += $get2['fixe'];
		$get1['internet'] += $get2['internet'];
		$get1['tv'] += $get2['tv'];
		$get1['mobile_small_qt'] += $get2['mobile_small_qt'];
		$get1['mobile_regular_qt'] += $get2['mobile_regular_qt'];
		$get1['mobile_heavy_qt'] += $get2['mobile_heavy_qt'];
		$get1['mobile_heavy_int_qt'] += $get2['mobile_heavy_int_qt'];
		if (isset($get2['fix_usage']) && $get2['fix_usage'] !== '') {
			$get1['fix_usage'] = $get2['fix_usage'];
		}
		if (isset($get2['internet_usage']) && $get2['internet_usage'] !== '') {
			$get1['internet_usage'] = $get2['internet_usage'];
		}
		
		return $get1;
	}

	public function orderByDisplayedPrice ($products) {
		foreach ($products as $k => $product) {
			$products[$k]['displayed_price'] = $product['discounted_price_period'] > 0 ? $product['discounted_price'] : $product['price'];
		}
		$ordered_products = Hash::sort($products, '{n}.displayed_price', 'asc');
		return $ordered_products;
	}

	/**
	 * @param $tag_group_id
	 * @param $product
	 *
	 * return mixed (bool) false if empty | (array) tags
	 */
	public function getTags( $tag_group_id, $product) {
		$tags = false;
		$product_tags = Hash::get($product, 'tag', null);
		if(is_array($product_tags)) {
			foreach ($product_tags as $tag) {
				if(Hash::get($tag, 'tag_group_id', null) == $tag_group_id) {
					$tags[] = $tag;
				}
			}
		}
		return $tags;
	}
}