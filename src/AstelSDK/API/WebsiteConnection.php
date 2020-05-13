<?php

namespace AstelSDK\API;

use AstelSDK\AstelContext;
use CakeUtility\Hash;

class WebsiteConnection extends APIModel {
	
	protected $disableCache = true;
	
	protected function getFirst(array $params = []) {
		$default_params = [
			'unique_visitor_key' => AstelContext::getUniqueVisitorKey(),
			'language' => $this->context->getLanguage(),
		];
		$params = Hash::merge($default_params, $params);
		$query = $this->newQuery();
		$query->addGETParams($params);
		$query->setUrl('v2_00/website_connection/');
		
		return $query->exec();
	}
	
	public function clearCart() {
		$params = [
			'unique_visitor_key' => AstelContext::getUniqueVisitorKey(),
		];
		$query = $this->newQuery();
		$query->setUrl('v2_00/website_connection/cart');
		$query->addGETParams($params);
		$query->setHTTPMethod(APIQuery::HTTP_DELETE);
		
		return $query->exec();
	}
}