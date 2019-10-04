<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class NewsRender extends APIModel {
	
	protected function getFirst(array $params = []) {
		$query = $this->newQuery();
		$id = Hash::get($params, 'id');
		if ($id === null || !is_numeric($id)) {
			return false;
		}
		unset($params['id']);
		$query->setUrl('news/render/' . $id);
		$query->addGETParams($params);
		
		return $query->exec();
	}
}