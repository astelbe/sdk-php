<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class CashbackClaim extends APIModel {
	
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
		$query->setUrl('v2_00/order/cashback_claim/');
		
		$defaultData = [
			'user_ip' => $this->context->getUserIP(),
		];
		$data = Hash::merge($defaultData, $data);

		
		$query->addPOSTParams($data);

		return $query->exec();
	}
}