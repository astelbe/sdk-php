<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class HardwareDeclination extends APIModel {
	
	protected function getAll(array $params = []) {
		$query = $this->newQuery();
		$query->setUrl('v2_00/hardware/declination');
		$default_params = [
		];
		if ($this->context->getIsPrivate() !== null) {
			if ($this->context->getIsPrivate()) {
				$default_params['is_private'] = 1;
			} else {
				$default_params['is_professional'] = 1;
			}
		}
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
		$query->setUrl('v2_00/hardware/declination/' . $id);
		$query->addGETParams($params);
		
		return $query->exec();
	}
}