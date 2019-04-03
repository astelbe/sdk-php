<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class PostalCode extends APIModel {
	
	protected function getAll(array $params = []) {
		$query = $this->newQuery();
		$query->setUrl('v2_00/postal_code');
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
		$query->setUrl('v2_00/postal_code/' . $id);
		$query->addGETParams($params);
		
		return $query->exec();
	}
}