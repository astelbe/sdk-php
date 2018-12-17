<?php

namespace AstelSDK\API;

use AstelSDK\QueryManager;

class Lead extends QueryManager implements IApiProducer {
	
	const CONTACT_TYPES = ['CALLBACK', 'MESSAGE'];
	const CONTACT_TYPE_CALLBACK = 'CALLBACK';
	const CONTACT_TYPE_MESSAGE = 'MESSAGE';
	const CONTACT_TOPICS = ['LEAD', 'AFTER_SALES'];
	const CONTACT_TOPIC_LEAD = 'LEAD';
	const CONTACT_TOPIC_AFTER_SALES = 'AFTER_SALES';
	
	public function createFirst(array $data = []) {
		$this->init();
		$url = 'v2_00/lead/';
		$this->setUrl($url);
		
		$defaultData = [
			'referer_page' => 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
			'user_ip' => $this->getUserIP(),
		];
		$data = array_merge($defaultData, $data);
		
		$this->setPost($data);
		
		return $this->exec(self::RETURN_MULTIPLE_ELEMENTS);
	}
}