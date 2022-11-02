<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class Tag extends APIModel {

	protected function getAll(array $params = []) {
		$query = $this->newQuery();
		$query->setUrl('v2_00/tag');
		$default_params = [
//			'accessible_as_page_in_front' => 1,
		];

		$params = Hash::merge($default_params, $params);

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
		$query->setUrl('v2_00/tag/' . $id);
		$query->addGETParams($params);

		return $query->exec();
	}


}
