<?php

namespace AstelSDK\API;

use CakeUtility\Hash;
use AstelSDK\Utils\URL;

class Tips extends APIModel {
	
	protected function getAll(array $params = []) {
		$query = $this->newQuery();
		$query->setUrl('v2_00/tips');
		if (isset($params['search_slug'])) {
			$params['search_slug'] = URL::base64url_encode($params['search_slug']);
		}
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
		$query->setUrl('v2_00/tips/' . $id);
		$query->addGETParams($params);
		
		return $query->exec();
	}
}