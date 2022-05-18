<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class TagGroup extends APIModel {

	protected function getAll(array $params = []) {
		$query = $this->newQuery();
		$query->setUrl('v2_00/tag_group');
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
		$query->setUrl('v2_00/tag_group/' . $id);
		$query->addGETParams($params);

		return $query->exec();
	}



}
