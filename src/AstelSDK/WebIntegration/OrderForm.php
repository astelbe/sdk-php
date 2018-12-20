<?php

namespace AstelSDK\WebIntegration;

use CakeUtility\Hash;
use AstelSDK\QueryManager;

class OrderForm extends QueryManager {
	
	public function getCSSList($allRequired = true) {
		$cssList = [
			'https://order' . $this->context->getEnv() . '.astel.be/css/order/orderform.css?v=' . $this->context->getVersion(),
		];
		if ($allRequired) {
			$cssList[] = 'https://cdn' . $this->context->getEnv() . '.astel.be/libs/bootstrap/4.0.0/css/bootstrap.min.css';
		}
		
		return $cssList;
	}
	
	public function getJSList() {
		return [
			'https://files' . $this->context->getEnv() . '.astel.be/DJs/astelContentInjector.js?v=' . $this->context->getVersion(),
			'https://order' . $this->context->getEnv() . '.astel.be/orderForms/orderform.js?v=' . $this->context->getVersion(),
		];
	}
	
	public function getCSS($allRequired = true) {
		$out = '';
		$cssList = $this->getCSSList($allRequired);
		foreach ($cssList as $css) {
			$out .= '<link rel="stylesheet" href="' . $css . '" />';
		}
		
		return $out;
	}
	
	public function getJS() {
		$out = '';
		$jsList = $this->getJSList();
		foreach ($jsList as $js) {
			$out .= '<script type="text/javascript" src="' . $js . '"></script>';
		}
		
		return $out;
	}
	
	public function getScriptOrderProduct($productID) {
		return '<script>
			/* setup the call below with paramaters:
			* language: uppercase string (FR, NL, EN, DE)
			* productId: the id of the product to buy
			* i.e.: getAstelOrderForm("FR", 1191, "orderForm");
			* See ID list via API
			*/
			getAstelOrderForm(\'' . $this->context->getLanguage() . '\', \'' . $productID . '\', \'orderForm\');
		</script>';
	}
	
	public function getScriptOrderToken() {
		global $_GET;
		$params = [];
		$params['token'] = Hash::get($_GET, 'token', '');
		$params['postal_code'] = Hash::get($_GET, 'postal_code', '');
		$urlParams = http_build_query($params);
		
		return '<script>
			/* setup the call below with paramaters:
			* language: uppercase string (FR, NL, EN, DE)
			* productId: the id of the product to buy
			* i.e.: getAstelOrderForm("FR", 1191, "orderForm");
			*/
			
			getAstelOrderForm(\'' . $this->context->getLanguage() . '\', \'' . $urlParams . '\', \'orderForm\');
			</script>
		';
	}
	
	public function getBodyLoadHtml() {
		return '<div id="orderForm">
				<div class="loadOrderFormTxt">
					<center><img class="loading-transparent"
					src="https://cdn' . $this->context->getEnv() . '.astel.be/assets/astelbefr/img/loading-transparent.gif"></center>
					<!--Image for loading-->
				</div>
			</div> ';
	}
	
	public function getOrderConfirmation() {
		global $_GET;
		$token = Hash::get($_GET, 'token');
		if (null === $token || !preg_match('/^[a-f0-9]{32}$/', $token)) {
			return 'no_valid_token_given';
		}
		$this->setApiParticle('order');
		$this->init();
		$this->setUrl('display/orderConfirmation/' . $this->context->getPartnerToken() . '/' . $token . '/' .
			$this->context->getLanguage());
		
		return $this->exec(self::RETURN_CONTENT);
		
	}
}