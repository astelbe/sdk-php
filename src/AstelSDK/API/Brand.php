<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class Brand extends APIModel {
	
	protected function getAll(array $params = []) {
		$query = $this->newQuery();
		$query->setUrl('v2_00/brand');
		$default_params = [
			'is_listable' => 1,
		];
		if ($this->context->getIsPrivate()) {
			$default_params['is_private'] = 1;
		} else {
			$default_params['is_professional'] = 1;
		}
		$default_params['order'] = 'display_weight_' . strtolower($this->context->getLanguage());
		$params = Hash::merge($default_params, $params);
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
		$query->setUrl('v2_00/brand/' . $id);
		$query->addGETParams($params);
		
		return $query->exec();
	}
}