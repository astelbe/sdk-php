<?php

namespace AstelSDK\WebIntegration;

use AstelSDK\AstelContext;
use AstelSDK\Utils\Singleton;
use AstelSDK\Utils\URL;

class HardwareShop extends Singleton {

	public function __construct() {
		$this->context = AstelContext::getInstance();
	}

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
		$params = [];
		$params['is_professional'] = $this->context->getIsProfessional();
		if ($brand_slug !== null) {
			$params['brand_slug'] = $brand_slug;
		}
		if ($view !== null) {
			$params['brand_slug'] = $brand_slug;
			$params['view'] = $view;
		}
		$serialize = serialize($params);
		$paramsURL = URL::base64url_encode($serialize);

		return '<script>
			getHardwareSelect("hardwareDiv", "' . $this->context->getLanguage() . '", "' . $paramsURL . '");
		</script>';
	}

	public function getScriptLoadHardwareDisplay($hardware_slug, $hardware_id = null, $hardwareIndexUrl = false, $offers_brand = null) {
		$serialize = serialize([
			'slug' => $hardware_slug,
			'id' => $hardware_id,
			'hardwareIndexUrl' => $hardwareIndexUrl,
			'offers_brand' => $offers_brand,
			'is_professional' => $this->context->getIsProfessional(),
		]);
		$paramsURL = URL::base64url_encode($serialize);

		return '<script>
			getHardwareDisplay("hardwareDiv", "' . $this->context->getLanguage() . '", "' . $paramsURL . '");
		</script>';
	}

	public function getBodyLoadHtml() {
		return '<article id="hardwareDiv" class="container">
				<div class="loadingImg text-center">
					<div class="spinner-border text-blue" style="width: 5rem; height: 5rem;" role="status">
						<span class="sr-only">Loading...</span>
					</div>
				</div>
			</article>';
	}
}