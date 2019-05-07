<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class WebsiteConnection extends APIModel {
	
	protected function getFirst(array $params = []) {
		$default_params = [
			'unique_visitor_key' => $this->context->getUniqueVisitorKey(),
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
			'unique_visitor_key' => $this->context->getUniqueVisitorKey(),
		];
		$query = $this->newQuery();
		$query->setUrl('v2_00/website_connection/cart');
		$query->addGETParams($params);
		$query->setHTTPMethod($query::HTTP_DELETE);
		
		return $query->exec();
	}
}