<?php

namespace AstelSDK\WebIntegration;

use AstelSDK\Utils\URL;
use AstelSDK\Utils\EncryptData;
use CakeUtility\Hash;

class HardwareShop extends AbstractWebIntegration {
	
	public function getCSSList($allRequired = true) {
		$cssList = [];
		
		if ($allRequired) {
			$cssList[] = 'https://cdn' . $this->context->getEnv() . '.astel.be/libs/bootstrap/4.3.1/css/bootstrap.min.css';
			$cssList[] = 'https://cdn' . $this->context->getEnv() . '.astel.be/libs/font-awesome/4.7.0/css/font-awesome.min.css';
		}
		$cssList[] = 'https://hardware' . $this->context->getEnv() . '.astel.be/css/hardware/hardware.css?v=' . $this->context->getVersion();
		
		return $cssList;
	}
	
	public function getJSList() {
		return [
			'https://files' . $this->context->getEnv() . '.astel.be/DJs/astelContentInjector.js?v=' .
			$this->context->getVersion(),
			'https://hardware' . $this->context->getEnv() . '.astel.be/hardware/inject.js?v=' . $this->context->getVersion(),
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
	
	public function getScriptLoadHardwareSelect($brand_slug = null, $view = null) {
		global $_GET;

		$params = [];
		$params['is_professional'] = $this->context->getIsProfessional();
		if ($brand_slug !== null) {
			$params['brand_slug'] = $brand_slug;
		}
		if ($view !== null) {
			$params['view'] = $view;
		}
		$params['session_id'] = $this->context->getSessionID();
		$params['page_url'] = $this->getPageURL();
		
		$username = Hash::get($_GET, 'username');
		if ($username !== null) {
			$params['username'] = $username;
		}
    $encryptionKey = $this->context->getEncryptionKey();
    $getParamsStr = json_encode($params);
		$encryptedGetParams = EncryptData::encrypt($getParamsStr, $encryptionKey);

		return '<script>
			getHardwareSelect("hardwareDiv", "' . $this->context->getLanguage() . '", "' . $encryptedGetParams . '");
		</script>';
	}
	
	public function getScriptLoadHardwareDisplay($hardware_slug, $hardware_id = null, $hardwareIndexUrl = false, $offers_brand = null) {
		global $_GET;
		
		$username = Hash::get($_GET, 'username');
		$serialize = serialize([
			'slug' => $hardware_slug,
			'id' => $hardware_id,
			'hardwareIndexUrl' => $hardwareIndexUrl,
			'offers_brand' => $offers_brand,
			'is_professional' => $this->context->getIsProfessional(),
			'session_id' => $this->context->getSessionID(),
			'page_url' => $this->getPageURL(),
			'username' => $username
		]);
		$paramsURL = URL::base64url_encode($serialize);
		
		return '<script>
			getHardwareDisplay("hardwareDiv", "' . $this->context->getLanguage() . '", "' . $paramsURL . '");
		</script>';
	}
	
	public function getBodyLoadHtml() {
		return '<article id="hardwareDiv" class="container">
				<div class="loadingImg text-center">
					<div class="spinner-border text-blue" style="width: 5rem; height: 5rem;margin-top:3rem;" role="status">
						<span class="sr-only">Loading...</span>
					</div>
					' . $this->txtToDisplayNoCookieTechnicalIssue() . '
				</div>
			</article>';
	}
}