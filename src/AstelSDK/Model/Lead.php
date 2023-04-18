<?php

namespace AstelSDK\Model;

class Lead extends SDKModel {
	protected $associated_instance_name = '\AstelSDK\API\Lead';
	
	const CONTACT_TYPES = ['CALLBACK', 'MESSAGE'];
	const CONTACT_TYPE_CALLBACK = 'CALLBACK';
	const CONTACT_TYPE_MESSAGE = 'MESSAGE';
	const CONTACT_TOPICS = ['LEAD', 'AFTER_SALES'];
	const CONTACT_TOPIC_LEAD = 'LEAD';
	const CONTACT_TOPIC_AFTER_SALES = 'AFTER_SALES';
	const CONTACT_TOPIC_GDPR = 'GDPR';

}