<?php

namespace AstelShared;

use AstelSDK\Utils\Singleton;
use CakeUtility\Hash;

class SharedView extends Singleton {
	
	public function render($path, $params = []) {

		include __DIR__ . '/../AstelShared/View/' . $path . '.php';
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
//					return d__('product', 'tab_mobile_unlimited_call');
					return self::getTranslation($translation_domain, 'tab_mobile_unlimited_call', $version, $description);
				} else if ($description == 'EWE') {
					return d__('product', 'tab_fixe_EWE');
				} else {
					if ($description == 0) {
						return '/';
					}
					return d__('product', 'tab_mobile_minutes' . $responsive, $description);
				}
			// TV : info has no translation keys with data injected
			default:
//				return $description;
			case 'play_description.tv.tab_tv_number_of_channel' :
				return self::getTranslation($translation_domain, 'tab_tv_number_of_channel', $version, $description);
			break;
			case 'play_description.tv.decoder_application' :
				return self::getTranslation($translation_domain, 'tab_tv_decoder_application', $version, $description);
			break;
			case 'play_description.tv.decoder_only' :
				return self::getTranslation($translation_domain, 'tab_tv_decoder_only', $version, $description);
			break;
			case 'play_description.tv.application_only' :
				return self::getTranslation($translation_domain, 'tab_tv_application_without_decoder', $version, $description);
		}
	}
	
	static function getMobileData($product, $version = 'front', $language = 'fr') {
		$data = [];
		//debug($product['play_description']['mobile']);
		$data['included_data_volume'] = self::getTranslatedPlayDescription( 'play_description.mobile.included_data_volume', $product, $version);
		$data['included_sms'] = self::getTranslatedPlayDescription( 'play_description.mobile.included_sms', $product, $version);
		$data['included_minutes_calls'] = self::getTranslatedPlayDescription( 'play_description.mobile.included_minutes_calls', $product, $version);
		$data['price_description'] = Hash::get($product, 'play_description.mobile.price_description.'.$language);
		return $data;
	}
	
	static function getInternetData($product, $version = 'front', $language = 'fr') {
		$data = [];
		$data['bandwidth_download'] = self::getTranslatedPlayDescription( 'play_description.internet.bandwidth_download', $product, $version);
		$data['bandwidth_volume'] = self::getTranslatedPlayDescription( 'play_description.internet.bandwidth_volume', $product, $version);
		$data['price_description'] = Hash::get($product, 'play_description.internet.price_description.'.$language);
		return $data;
	}
	
	static function getTVData($product, $version = 'front', $language = 'fr') {
		$data = [];
		$data['included_data_volume'] = self::getTranslatedPlayDescription( 'play_description.tv.number_tv_channel', $product, $version);
		$data['decoder_application'] = self::getTranslatedPlayDescription( 'play_description.tv.decoder_application', $product, $version);
		$data['decoder_only'] = self::getTranslatedPlayDescription( 'play_description.tv.decoder_only', $product, $version);
		$data['application_only'] = self::getTranslatedPlayDescription( 'play_description.tv.application_only', $product, $version);
		return $data;
	}
	
	static function getFIXData($product, $version = 'front', $language = 'fr') {
		$data = [];
		$data['included_minutes_calls'] = self::getTranslatedPlayDescription( 'play_description.fix.included_minutes_calls', $product, $version);
		return $data;
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

