<?php

namespace AstelSDK\Model;

use AstelSDK\Utils\SingletonAssociated;

abstract class SDKModel extends SingletonAssociated {
	
	public function combineCollectionIDArrayKey(array $items) {
		$out = [];
		
		foreach ($items as $item) {
			$idItem = Hash::get($item, 'id');
			$out[$idItem] = $item;
		}
		
		return $out;
	}
}