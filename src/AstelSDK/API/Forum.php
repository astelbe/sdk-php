<?php

namespace AstelSDK\API;

class Forum extends APIModel {
	
	protected function getAll(array $params = []) {
		$query = $this->newQuery();
		$query->setUrl('forum/latest');
		
		return $query->exec();
	}
}