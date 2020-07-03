<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class Lead extends APIModel {
	
	/**
	 * @param array $data
	 *
	 * @return array
	 * @throws \ValidationErrorException
	 * @throws \AstelSDK\Exception\DataException
	 * @throws \Exception
	 */
	public function createFirst(array $data = []) {
		$query = $this->newQuery();
		$query->setUrl('v2_00/lead/');
		
		$defaultData = [
			'referer_page' => $this->context->getReferrer(),
			'user_ip' => $this->context->getUserIP(),
		];
		$data = Hash::merge($defaultData, $data);
		
		$query->addPOSTParams($data);
		
		return $query->exec();
	}
}