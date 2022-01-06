<?php

namespace AstelShared;

use AstelSDK\Utils\Singleton;
use CakeUtility\Hash;
use AstelSDK\AstelContext;

class SharedView extends Singleton {
	
	public function render($path, $params = []) {

		include __DIR__ . '/../AstelShared/View/' . $path . '.php';
	}


	/**
	 * @param array $options
	 * - 'full_postal_code' array - Postal code from db, get by postal_code_id from session
	 * - other options : cf AstelShared/Typeahead.php attributes and __construct
	 *
	 * @return string - typeahead's html
	 */
	// TODO Params non de callback apiGateway
	public function getPostalCodeTypeahead ($options = []) {
		$Context = AstelContext::getInstance();
		$typeahead = new Typeahead($options);
		$full_postal_code = Hash::get($options,'full_postal_code', null);
		if (!empty($full_postal_code)) {
			// BC, OrderRequest postal codes are already processed with the translated name in 'city_name'
			if (Hash::get($full_postal_code, 'city_name', null)) {
				$name = Hash::get($full_postal_code, 'city_name', null);
			} else {
				$name = Hash::get($full_postal_code, 'name.' . $Context->getLanguage());
			}
			$typeahead->input_value = Hash::get($full_postal_code, 'postal_code') . ' - ' . $name;
			$typeahead->hidden_input_value = Hash::get($full_postal_code, 'id') ;
		}
		return $typeahead->getHtml($options);
	}

	// TODO move in typeahead class
	public function getTypeaheadScripts () {
		$Context = AstelContext::getInstance();
		$out = '';
		$jsList = [
			'https://files' . $Context->getEnv() . '.astel.be/DJs/astelPostalCodes/postal_codes_' . $Context->getLanguage() . '.js?v=' . $Context->getVersionData(),
			'https://files' . $Context->getEnv() . '.astel.be/DJs/typeahead.js?v=' . $Context->getVersion(),
		];
		foreach ($jsList as $js) {
			$out .= '<script type="text/javascript" src="' . $js . '"></script>';
		}

		return $out;
	}



	/**
	 * @param $playDescriptionPath in $product array
	 * @param $product
	 * @param string $version. 'front' or 'cake', to get the domain name used in translation keys
	 * @param null $responsive
	 *
	 * @return null|string $description
	 */
	static function getTranslatedPlayDescription($playDescriptionPath, $product, $version = 'front', $responsive = null) {
		$description = Hash::get($product, $playDescriptionPath, null);
		if (!$description) {
			return null;
		}
		if($version == 'front') {
			$translation_domain = 'product';
		} else {
			$translation_domain = 'OrderAstelBe';
		}

		switch ($playDescriptionPath) {

			// MOBILE
			case 'play_description.mobile.included_minutes_calls' :
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain, 'tab_mobile_unlimited_call', $version);
				} else {
					return self::getTranslation($translation_domain, 'tab_mobile_minutes' . $responsive, $version, $description);
				};
				break;
			case 'play_description.mobile.included_data_volume' :
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain,'tab_mobile_unlimited_internet', $version);
				} else {
					return self::getTranslation($translation_domain,'tab_mobile_gb_data' . $responsive, $version,$description / 1000);
				}
				break;
			case 'play_description.mobile.included_sms' :
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain, 'tab_mobile_unlimited_sms', $version);
				} else {
					return self::getTranslation($translation_domain, 'tab_mobile_sms', $version, $description);
				}
				break;
			// INTERNET
			case 'play_description.internet.bandwidth_download' :
				return self::getTranslation($translation_domain, 'tab_internet_mbps', $version, $description);
				break;
			case 'play_description.internet.bandwidth_upload' :
				return self::getTranslation($translation_domain, 'tab_internet_mbps', $version, $description);
				break;
			case 'play_description.internet.bandwidth_volume' :
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain, 'tab_unlimited', $version);
				} else {
					return self::getTranslation($translation_domain, 'tab_mobile_gb_data', $version, $description);
				}
			// FIX
			case 'play_description.fix.included_minutes_calls' :
				if ($description == 'UNLIMITED') {
					return d__('product', 'tab_mobile_unlimited_call');
				} else if ($description == 'EWE') {
					return d__('product', 'tel_EWE');
				} else {
					if ($description == 0) {
						return '/';
					}
					return d__('product', 'tab_mobile_minutes' . $responsive, $description);
				}
			// TV : info has no translation keys with data injected
			default:
				return $description;
		}
	}


	static function getTranslation($domain, $key, $version, $params = []) {
		switch ($version) {
			case 'front' :
				return d__($domain, $key, $params);
				break;
			case 'cake' :
				return __d($domain, $key, $params);
		}
	}
	
	public function renderStar($quality_score) {


		$html = '<div class="d-inline">';

		$quality = $quality_score / 20;
		$quality = ceil($quality * 2) / 2;
		?>
		<?php
		$s = 0;
		$fullStars = floor($quality);
		while ($s < $fullStars) {
			$html  .= '<i class="fa fa-star fa-lg"></i>';
			$s++;
		}
		$halfStars = ceil($quality) - $fullStars;
		$s = 0;
		while ($s < $halfStars) {
			$html  .= '<i class="fa fa-star-half-o fa-lg"></i>';
			$s++;
		}
		$emptyStats = 5 - $fullStars - $halfStars;
		$s = 0;
		while ($s < $emptyStats) {
			$html  .= '<i class="fa fa-star-o fa-lg"></i>';
			$s++;
		}

		$html .= '</div>';

		return $html;

	}
}

