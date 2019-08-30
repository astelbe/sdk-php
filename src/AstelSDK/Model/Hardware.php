<?php

namespace AstelSDK\Model;

use CakeUtility\Set;

class Hardware extends SDKModel {
	
	protected $associated_instance_name = '\AstelSDK\API\Hardware';
	
	/**
	 * @param $prodDB
	 *
	 * @return array
	 */
	public function hardwareArrayToSelectableList($prodDB) {
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
	
}
