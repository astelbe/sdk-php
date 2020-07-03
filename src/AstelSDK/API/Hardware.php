<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class Hardware extends APIModel {
	
	protected function getAll(array $params = []) {
		$query = $this->newQuery();
		$query->setUrl('v2_00/hardware');
		$default_params = [
		];
		$params = Hash::merge($default_params, $params);
		if (isset($params['search_slug'])) {
			$params['search_slug'] = URL::base64url_encode($params['search_slug']);
		}
		if (isset($params['search_brand_slug'])) {
			$params['search_brand_slug'] = URL::base64url_encode($params['search_brand_slug']);
		}
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
		$query->setUrl('v2_00/hardware/' . $id);
		$query->addGETParams($params);
		
		return $query->exec();
	}
}