<?php

namespace AstelSDK\API;

use AstelSDK\Model;
use CakeUtility\Hash;

/**
 * Class DiscountApi
 */
class Discount extends Model implements IApiConsumer {
	
	protected function getAll(array $params = []) {
		$query = $this->newQuery();
		$query->setUrl('v2_00/discount');
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
		$query->setUrl('/discount/' . $id);
		$query->addGETParams($params);
		
		return $query->exec();
	}
}