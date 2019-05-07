<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class OptionGroup extends APIModel {
	
	protected function getAll(array $params = []) {
		$query = $this->newQuery();
		$query->setUrl('v2_00/option_group');
		
		$query->addGETParams($params);
		
		return $query->exec();
	}
	
	protected function getFirst(array $params = []) {
		$query = $this->newQuery();
		$id = Hash::get($params, 'id');
		if ($id === null || !is_numeric($id)) {
			return false;
		}
		unset($params['id']);
		$query->setUrl('v2_00/option_group/' . $id);
		$query->addGETParams($params);
		
		return $query->exec();
	}
}