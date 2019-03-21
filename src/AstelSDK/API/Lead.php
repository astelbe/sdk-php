<?php

namespace AstelSDK\API;

use AstelSDK\APIModel;

class Lead extends APIModel implements IApiProducer {
	
	const CONTACT_TYPES = ['CALLBACK', 'MESSAGE'];
	const CONTACT_TYPE_CALLBACK = 'CALLBACK';
	const CONTACT_TYPE_MESSAGE = 'MESSAGE';
	const CONTACT_TOPICS = ['LEAD', 'AFTER_SALES'];
	const CONTACT_TOPIC_LEAD = 'LEAD';
	const CONTACT_TOPIC_AFTER_SALES = 'AFTER_SALES';
	
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