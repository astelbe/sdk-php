<?php

namespace AstelSDK\API;

use AstelSDK\Model;
use CakeUtility\Hash;

class Partner extends Model implements IApiConsumer {
	
	protected function getFirst(array $params = []) {
		$default_params = [
			'_embed' => 'last_ordered_products,call_center_open',
		];
		$params = Hash::merge($default_params, $params);
		
		$query = $this->newQuery();
		
		$query->addGETParams($params);
		$query->setUrl('v2_00/partner/');
		
		return $query->exec();
	}
}
