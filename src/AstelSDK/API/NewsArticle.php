<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class NewsArticle extends APIModel {
	
	protected function getAll(array $params = []) {
		$query = $this->newQuery();
		$query->setUrl('v2_00/news/article');
		
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
		$query->setUrl('v2_00/news/article/' . $id);
		$query->addGETParams($params);
		
		return $query->exec();
	}
}