<?php

namespace AstelSDK\Model;

use CakeUtility\Hash;

class Discount extends SDKModel {
	
	protected $associated_instance_name = '\AstelSDK\API\Discount';

	/**
	 * @param array $discount. Required to give discount with embeded subscription_periods/product
	 */
	public function countProducts(array $discount, $scope_type = 'is_private') {
		$countProducts = 0;
		$subscription_periods = Hash::get($discount,  'subscription_periods', '');
		foreach($subscription_periods as $subscription_period) {
			if($subscription_period['product'] && Hash::get($subscription_period, 'product.' . $scope_type)) {
				$countProducts ++;
			}
		}

		return $countProducts;
	}
	
}