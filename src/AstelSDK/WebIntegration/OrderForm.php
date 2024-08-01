<?php

namespace AstelSDK\WebIntegration;

use AstelSDK\API\APIQuery;
use AstelSDK\EmulatedSession;
use AstelSDK\AstelContext;
use CakeUtility\Hash;
use AstelShared\Typeahead;


class OrderForm extends AbstractWebIntegration
{

	public function getCSSList($allRequired = true)
	{
		$cssList = [];

		if ($allRequired) {
			$cssList[] = 'https://cdn' . $this->context->getEnv() . '.astel.be/libs/bootstrap/4.3.1/css/bootstrap.min.css';
			$cssList[] = 'https://cdn' . $this->context->getEnv() . '.astel.be/libs/font-awesome/4.7.0/css/font-awesome.min.css';
		}
		$cssList[] = 'https://order' . $this->context->getEnv() . '.astel.be/css/order/orderform.css?v=' . $this->context->getVersion();

		return $cssList;
	}

	public function getJSList()
	{
		$Typeahead = Typeahead::getInstance();
		$typeahead_js = $Typeahead->getJsList();
		$order_form_js =  [
			'https://files' . $this->context->getEnv() . '.astel.be/DJs/astelContentInjector.js?v=' . $this->context->getVersion(),
			'https://order' . $this->context->getEnv() . '.astel.be/orderForms/inject.js?v=' . $this->context->getVersion(),
		];
		return array_merge($typeahead_js, $order_form_js);
	}

	public function getCSS($allRequired = true)
	{
		$out = '';
		$cssList = $this->getCSSList($allRequired);
		foreach ($cssList as $css) {
			$out .= '<link rel="stylesheet" href="' . $css . '" />';
		}

		return $out;
	}

	public function getJS()
	{
		$out = '';
		$jsList = $this->getJSList();
		foreach ($jsList as $js) {
			$out .= '<script type="text/javascript" src="' . $js . '"></script>';
		}

		return $out;
	}

	public function getScriptOrderProduct($productID)
	{
		global $_GET;

		$params = ['data' => []];
		$params['data']['product_id'] = $productID;
		$postal_code = Hash::get($_GET, 'postal_code');
		if ($postal_code !== null) {
			$params['data']['postal_code'] = $postal_code;
		}
		$postal_code_id = Hash::get($_GET, 'postal_code_id');
		if ($postal_code_id !== null) {
			$params['data']['postal_code_id'] = $postal_code_id;
		}
		$hardware_product_id = Hash::get($_GET, 'hardware_product_id');
		if ($hardware_product_id !== null) {
			$params['data']['hardware_product_id'] = $hardware_product_id;
		}

		// TODO add doc about how it is possible to have $_GET['has_user_cookie_consent'] or $extraParams['has_user_cookie_consent']
		$has_user_cookie_consent = Hash::get($_GET, 'has_user_cookie_consent', false);

		$username = Hash::get($_GET, 'username');
		if ($username !== null) {
			$params['data']['username'] = $username;
		}

		$partner_user_id = Hash::get($_GET, 'partner_user_id');
		if ($partner_user_id !== null) {
			$params['data']['partner_user_id'] = $partner_user_id;
		}
		$overridePartnerId = Hash::get($_GET, 'partnerID');
		if ($overridePartnerId !== null) {
			$params['data']['override_partner_id'] = $overridePartnerId;
		}

		$params['data']['page_url'] = $this->getPageURL();
		$urlParams = http_build_query($params);

		return '<script>
			/* setup the call below with paramaters:
			* language: uppercase string (FR, NL, EN, DE)
			* productId: the id of the product to buy
			* i.e.: getAstelOrderForm("FR", 1191, "orderForm");
			* See ID list via API
			*/
			getAstelOrderForm(\'' . $this->context->getLanguage() . '\', \'' . $urlParams . '\', \'orderForm\', \'' .
			$this->context->getSessionID() . '\', \'' . $has_user_cookie_consent . '\' );
		</script>';
	}

	public function getScriptOrderToken($extraParams = [])
	{
		global $_GET;
		$params = ['data' => []];
		$params['data']['product_arrangement_token'] = Hash::get($_GET, 'token', '');
		$postal_code = Hash::get($_GET, 'postal_code');
		if ($postal_code !== null) {
			$params['data']['postal_code'] = $postal_code;
		}
		$postal_code_id = Hash::get($_GET, 'postal_code_id');
		if ($postal_code_id !== null) {
			$params['data']['postal_code_id'] = $postal_code_id;
		}
		$hardware_product_id = Hash::get($_GET, 'hardware_product_id');
		if ($hardware_product_id !== null) {
			$params['data']['hardware_product_id'] = $hardware_product_id;
		}
		$username = Hash::get($_GET, 'username');
		if ($username !== null) {
			$params['data']['username'] = $username;
		}
		$partner_user_id = Hash::get($_GET, 'partner_user_id');
		if ($partner_user_id !== null) {
			$params['data']['partner_user_id'] = $partner_user_id;
		}
		foreach ($extraParams as $paramName => $paramValue) {
			$params['data'][$paramName] = $paramValue;
		}

		$params['data']['page_url'] = $this->getPageURL();
		$urlParams = http_build_query($params);

		// TODO add doc about how it is possible to have $_GET['has_user_cookie_consent'] or $extraParams['has_user_cookie_consent']
		if (Hash::get($_GET, 'has_user_cookie_consent', false)) {
			$has_user_cookie_consent = Hash::get($_GET, 'has_user_cookie_consent', false);
		} elseif (isset($extraParams['has_user_cookie_consent'])) {
			$has_user_cookie_consent = $extraParams['has_user_cookie_consent'];
		}

		return '<script>
			/* setup the call below with paramaters:
			* language: uppercase string (FR, NL, EN, DE)
			* productId: the id of the product to buy
			* i.e.: getAstelOrderForm("FR", 1191, "orderForm");
			*/
			
			getAstelOrderForm(\'' . $this->context->getLanguage() . '\', \'' . $urlParams . '\', \'orderForm\', \'' . $this->context->getSessionID() . '\', \'' . $has_user_cookie_consent . '\');
			</script>
		';
	}

	public function getBodyLoadHtml()
	{

		return '<div id="orderForm">
				<div class="loadOrderFormTxt text-center">
					<div class="spinner-border text-blue" style="width: 5rem; height: 5rem;margin-top:3rem;" role="status">
						<span class="sr-only">Loading...</span>
					</div>
					' . $this->txtToDisplayNoCookieTechnicalIssue() . '
				</div>
			</div> ';
	}

	public function getOrderConfirmation()
	{
		global $_GET;

		$token = Hash::get($_GET, 'token');
		if (null === $token || !preg_match('/^[a-f0-9]{32}$/', $token)) {
			return 'no_valid_token_given';
		}
		$query = new APIQuery('order');
		$query->setUrl('display/orderConfirmation/' . $this->context->getPartnerToken() . '/' . $token . '/' .
			$this->context->getLanguage());
		$result = $query->exec(APIQuery::RETURN_CONTENT);
		$errorMessage = 'Confirmation page retrieval failure, please contact us.';
		if ($result->isResultSucess()) {
			$resultData = $result->getResultData();

			return Hash::get($resultData, '0', $errorMessage);
		}

		return $errorMessage;
	}
}
