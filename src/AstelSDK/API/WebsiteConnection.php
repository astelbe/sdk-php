<?php

namespace AstelSDK\API;

use AstelSDK\AstelContext;
use CakeUtility\Hash;
use AstelSDK\Utils\URL;

class WebsiteConnection extends APIModel {
	
	protected $disableCache = true;
	
	protected function getFirst(array $params = []) {
		$default_params = [
			'unique_visitor_key' => $this->context->getSessionID(),
			'user_agent' => URL::base64url_encode(AstelContext::getUserAgent()),
			'remote_ip' => AstelContext::getUserIP(),
			'domain' => AstelContext::getCallingServerName(),
			'language' => $this->context->getLanguage(),
		];
		$sessionSalt = $this->context->session->getSessionSalt();
		if ($sessionSalt !== null) {
			$default_params['session_salt'] = $sessionSalt;
		}
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