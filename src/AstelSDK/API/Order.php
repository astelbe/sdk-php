<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class Order extends APIModel {
	
	protected $customCacheTTL = 300; // 5 min
	
	protected function getAll(array $params = []) {
		$query = $this->newQuery();
		$query->setUrl('v2_00/order');
		$query->addGETParams($params);
		
		return $query->exec();
	}
	
	protected function getFirst(array $params = []) {
		$id = Hash::get($params, 'id');
		if ($id === null || !is_numeric($id)) {
			return false;
		}
		unset($params['id']);
		$query = $this->newQuery();
		$query->setUrl('v2_00/order/' . $id);
		$query->addGETParams($params);
		
		return $query->exec();
	}
}