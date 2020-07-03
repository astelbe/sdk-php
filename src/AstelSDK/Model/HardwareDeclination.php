<?php

namespace AstelSDK\Model;

use CakeUtility\Hash;
use CakeUtility\Set;

class HardwareDeclination extends SDKModel {
	
	protected $associated_instance_name = '\AstelSDK\API\HardwareDeclination';
	
	/**
	 * @param $hardwareDeclination With embedded at least hardware.features.tag_group
	 *
	 * @return mixed $hardwareDeclination with their features ordered by tag group weight
	 */
	public function orderFeaturesByGroupWeight($hardwareDeclination) {
		$features = Hash::get($hardwareDeclination, 'hardware.features', []);
		if (!empty($features)) {
			$features = Set::sort($features, '{n}.tag_group.weight', 'asc');
			$hardwareDeclination['hardware']['features'] = $features;
		}
		
		return $hardwareDeclination;
	}
	
}
