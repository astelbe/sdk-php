<?php

namespace AstelSDK\API;

use AstelSDK\AstelContext;
use AstelSDK\EmulatedSession;
use CakeUtility\Hash;
use AstelSDK\Utils\URL;

class WebsiteConnection extends APIModel {
	
	protected $disableCache = true;
	
	protected function getFirst(array $params = []) {
		$default_params = [
			'user_agent' => URL::base64url_encode(AstelContext::getUserAgent()),
			'remote_ip' => AstelContext::getUserIP(),
			'domain' => AstelContext::getCallingServerName(),
			'language' => $this->context->getLanguage(),
			'is_professional' => $this->context->getIsProfessional(),
			'_embed' => 'partner/last_ordered_products,partner/call_center_open'
		];
		$default_params['no_trace'] = 1;
		if (EmulatedSession::isNavigatorAcceptingCookies()) {
			$default_params['no_trace'] = 0;
		}

		if($params['session_id'] && isset($this->context->partner_referral_id)) {
			$params['partner_referral_id'] = $this->context->partner_referral_id;
		}

    if($params['session_id'] && isset($this->context->has_user_cookie_consent)) {
      $params['has_user_cookie_consent'] = $this->context->has_user_cookie_consent;
    }

		if (!isset($params['session_id']) && $this->context->getSession() !== null) {
			// there is already a current session open
			$params['session_id'] = $this->context->getSession()->getSessionID();
			
		}
		
		if (!isset($params['session_id']) && $this->context->getSession() !== null) {
			// there is already a current session open
			$params['session_id'] = $this->context->getSession()->getSessionID();
			if (!isset($params['session_salt'])) {
				$params['session_salt'] = $this->context->getSession()->getSessionSalt();
			}
		}
		
		$params = Hash::merge($default_params, $params);
		$query = $this->newQuery();
		$query->addGETParams($params);
		$query->setUrl('v2_00/website_connection/');
		
		return $query->exec();
	}
	
	public function clearCart(array $params = []) {
		$default_params = [];
		if ($this->context->getSession() !== null) {
			$default_params['session_id'] = $this->context->getSession()->getSessionID();
			$default_params['user_agent'] = URL::base64url_encode(AstelContext::getUserAgent());
			$default_params['remote_ip'] = AstelContext::getUserIP();
			$default_params['domain'] = AstelContext::getCallingServerName();
			$default_params['language'] = $this->context->getLanguage();
		}
		$params = Hash::merge($default_params, $params);
		$query = $this->newQuery();
		$query->setUrl('v2_00/website_connection/cart');
		$query->addGETParams($params);
		$query->setHTTPMethod(APIQuery::HTTP_DELETE);
		
		return $query->exec();
	}

	public function updateSessionPostalCode (array $params = []) {
		$postal_code = Hash::get($params, 'postal_code_id', false);
		if ($postal_code && ctype_digit($postal_code) && strlen($postal_code) == 4) {
			$default_params = [];
			if ($this->context->getSession() !== null) {
				$default_params['session_id'] = $this->context->getSession()->getSessionID();
				$default_params['user_agent'] = URL::base64url_encode(AstelContext::getUserAgent());
				$default_params['remote_ip'] = AstelContext::getUserIP();
				$default_params['domain'] = AstelContext::getCallingServerName();
				$default_params['language'] = $this->context->getLanguage();
			} else {
				// TODO ?
			}
			$params = Hash::merge($default_params, $params);
			$query = $this->newQuery();
			$query->setUrl('v2_00/website_connection/' . $default_params['session_id']);
			$query->addPOSTParams($params);
			$query->setHTTPMethod(APIQuery::HTTP_POST);

			return $query->exec();
		} else {

			return ['error' => 'no postal code founded'];
		}
	}
}