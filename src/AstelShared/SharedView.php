<?php

namespace AstelShared;

use AstelSDK\Utils\Singleton;
use CakeUtility\Hash;

class SharedView extends Singleton {
	
	public function render($path, $params = []) {

//		$out = '';
		//ob_start();
		// Include
		include_once __DIR__ . '/../AstelShared/View/' . $path . '.php';
//		debug('ok');
		//$out = ob_get_contents();
		//ob_end_clean();
		
		//return $out;
		// TODO return string
	}


	static function getProductInfo($playDescriptionPath, $product, $version, $responsive = null) {
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

			// Mobile
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
			// Internet
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
}

