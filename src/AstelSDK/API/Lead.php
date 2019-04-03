<?php

namespace AstelSDK\API;

class Lead extends APIModel {
	
	public function createFirst(array $data = []) {
		$query = $this->newQuery();
		$query->setUrl('v2_00/lead/');
		
		$defaultData = [
			'referer_page' => $this->context->getReferrer(),
			'user_ip' => $this->context->getUserIP(),
		];
		$data = Hash::merge($defaultData, $data);
		
		$query->addPOSTParams($data);
		
		return $this->exec();
	}
}