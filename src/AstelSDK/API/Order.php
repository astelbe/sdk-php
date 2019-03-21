<?php

namespace AstelSDK\API;

use AstelSDK\APIModel;
use CakeUtility\Hash;

class Order extends APIModel implements IApiConsumer {
	
	protected function getAll(array $params = []) {
		$query = $this->newQuery();
		$query->setUrl('v1_10/getOrdersStatusList');
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
		$query->setUrl('v1_10/getOrdersStatusList/' . $id);
		$query->addGETParams($params);
		
		return $query->exec();
	}
}