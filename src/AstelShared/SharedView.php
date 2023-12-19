<?php

namespace AstelShared;

use AstelSDK\Utils\Singleton;
use AstelSDK\Model\Product;
use CakeUtility\Hash;

class SharedView extends Singleton {


	public $language = 'fr';
	public $version = 'front'; // 'front' or 'cake', to get the domain name used in translation keys

	public function render($path, $params = []) {
		include __DIR__ . '/../AstelShared/View/' . $path . '.php';
	}

	public function setLanguage ($language) {
		$this->language = $language;
	}

	public function setVersion($version) {
		$this->version = $version;
	}

	/**
	 * TODO Deprecated replaced by translatePlayDescription
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
					return self::getTranslation($translation_domain, 'tab_mobile_unlimited_call', $version);
				} else if ($description == 'EWE') {
//					return d__('product', 'tab_fixe_EWE');
					return self::getTranslation($translation_domain, 'tab_fixe_EWE', $version);
				} else {
					if ($description == 0) {
						return '/';
					}
//					return d__('product', 'tab_mobile_minutes' . $responsive, $description);
					return self::getTranslation($translation_domain, 'tab_mobile_minutes' . $responsive, $version, $description);
				}
			// TV : info has no translation keys with data injected
			case 'play_description.tv.number_tv_channel' :
				return self::getTranslation($translation_domain, 'tab_tv_number_of_channel', $version, $description);
			break;
			case 'play_description.tv.max_tv_channel' :
				return self::getTranslation($translation_domain, 'tab_max_tv_channel', $version, $description);
			break;
			case 'play_description.tv.decoder_application' :
				return self::getTranslation($translation_domain, 'tab_tv_decoder_application', $version, $description);
			break;
			case 'play_description.tv.tab_tv_decoder_only':
				return self::getTranslation($translation_domain, 'tab_tv_decoder_only', $version, $description);
			break;
			case 'play_description.tv.application_only' :
				return self::getTranslation($translation_domain, 'tab_tv_application_without_decoder', $version, $description);
			break;
			default:
				return $description;
		}
	}

	// TODO deprecated with getMobileDetails
	static function getMobileData($product, $version = 'front', $language = 'fr') {
		$Product = Product::getInstance();
		if ($Product->isType($product, 'M')) {
			$data = [];
			$data['included_data_volume'] = self::getTranslatedPlayDescription('play_description.mobile.included_data_volume',
				$product, $version);
			$data['included_sms'] = self::getTranslatedPlayDescription('play_description.mobile.included_sms', $product,
				$version);
			$data['included_minutes_calls'] = self::getTranslatedPlayDescription('play_description.mobile.included_minutes_calls',
				$product, $version);
			$data['price_description'] = '<p class="sub-details-infos toggle-details toggle-details-'. $key .'">' . Hash::get($product, 'play_description.mobile.price_description.'.$language) . '</p>';
			return $data;
		} else {
			return false;
		}
	}

	// TODO deprecated with getInternetDetails
	static function getInternetData($product, $version = 'front', $language = 'fr') {
		$Product = Product::getInstance();
		if ($Product->isType($product, 'I')) {
			$data = [];
			$data['bandwidth_download'] = self::getTranslatedPlayDescription( 'play_description.internet.bandwidth_download', $product, $version);
			$data['bandwidth_volume'] = self::getTranslatedPlayDescription( 'play_description.internet.bandwidth_volume', $product, $version);
			$data['price_description'] = '<p class="sub-details-infos toggle-details toggle-details-'. $key .'">' . Hash::get($product, 'play_description.internet.price_description.'.$language) . '</p>';
			return $data;
		} else {
			return false;
		}
	}
	// TODO deprecated with getTVDetails
	static function getTVData($product, $version = 'front', $language = 'fr') {
		$Product = Product::getInstance();
		if ($Product->isType($product, 'T')){
			$data = [];
//			$max_tv_channel = Hash::get($product, 'play_description.tv.size_number_tv_max');
			$decoderApplication = Hash::get($product, 'play_description.tv.decoder_application');
			$data['number_tv_channel'] = self::getTranslatedPlayDescription( 'play_description.tv.number_tv_channel', $product, $version);
			if(Hash::get($product, 'play_description.tv.decoder_application')) {
				$data['decoder_application'] = self::getTranslatedPlayDescription( 'play_description.tv.decoder_application', $product, $version, $decoderApplication);
			} elseif (Hash::get($product, 'play_description.tv.decoder_only')) {
//				$data['decoder_only'] = self::getTranslatedPlayDescription( 'play_description.tv.decoder_only', $product, $version, $decoderApplication);
				$data['decoder_only'] = "<span> Décodeur (max 1) (pas d'application TV) </span>";
			} else {
				$data['application_only'] = self::getTranslatedPlayDescription( 'play_description.tv.application_only', $product, $version, $decoderApplication);
			}
			$data['price_description'] = '<p class="sub-details-infos toggle-details toggle-details-'. $key .'">' . Hash::get($product, 'play_description.tv.price_description.'.$language) . '</p>';
			return $data;
		} else {
			return false;
		}
	}

	// TODO deprecated with getFixDetails
	static function getFixData($product, $version = 'front', $language = 'fr') {
		$Product = Product::getInstance();
		if ($Product->isType($product, 'F')) {
			$data = [];
			$data['included_minutes_calls'] = self::getTranslatedPlayDescription( 'play_description.fix.included_minutes_calls', $product, $version);
			$data['price_description'] = '<p class="sub-details-infos toggle-details toggle-details-'. $key .'">' . Hash::get($product, 'play_description.fix.price_description.'.$language) . '</p>';
			return $data;
		} else {
			return false;
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



	public function getGsmDetails ($product) {
		$Product = Product::getInstance();
		if ($Product->isType($product, 'M')) {
			$details = [];
			$details['included_data_volume'] = $this->translatePlayDescription('play_description.mobile.included_data_volume', $product);
			$details['included_sms'] = $this->translatePlayDescription('play_description.mobile.included_sms', $product);
			$details['included_minutes_calls'] = $this->translatePlayDescription('play_description.mobile.included_minutes_calls', $product);
			return [
				'details' => implode(', ', $details),
				'description' => Hash::get($product, 'play_description.mobile.price_description.'.$this->language),
				'label' => 'GSM',
				'count' => $product['count']
			];
		} else {
			return false;
		}
	}

	public function getInternetDetails($product) {
		$Product = Product::getInstance();
		if ($Product->isType($product, 'I')) {
			$data = [];
			$data['bandwidth_download'] = self::translatePlayDescription( 'play_description.internet.bandwidth_download', $product);
			$data['bandwidth_volume'] = self::translatePlayDescription( 'play_description.internet.bandwidth_volume', $product);
			return [
				'details' => implode(', ', $data),
				'description' => Hash::get($product, 'play_description.internet.price_description.'.$this->language),
				'label' => 'NET'
			];
		} else {
			return false;
		}
	}

	public function getFixDetails($product) {
		$Product = Product::getInstance();
		if ($Product->isType($product, 'F')) {
			return [
				'details' => self::translatePlayDescription( 'play_description.fix.included_minutes_calls', $product),
				'description' => Hash::get($product, 'play_description.fix.price_description.'.$this->language),
				'label' => ($this->language == 'FR' ? 'FIXE' : 'VAST')
			];
		} else {
			return false;
		}
	}

	public function getTVDetails($product) {
		$Product = Product::getInstance();
		if ($Product->isType($product, 'T')){
			$data = [];
			$data['number_tv_channel'] = self::translatePlayDescription( 'play_description.tv.number_tv_channel', $product);
			if(Hash::get($product, 'play_description.tv.decoder_application')) {
				// Replace boolean decoder_only by the number of tv max for translation
				$product['play_description']['tv']['decoder_application'] = Hash::get($product, 'play_description.tv.size_number_tv_max');
				$data['decoder_application'] = self::translatePlayDescription( 'play_description.tv.decoder_application', $product);
			} elseif (Hash::get($product, 'play_description.tv.decoder_only')) {
				// Replace boolean decoder_only by the number of tv max for translation
				$product['play_description']['tv']['decoder_only'] = Hash::get($product, 'play_description.tv.size_number_tv_max');
				$data['decoder_only'] = self::translatePlayDescription( 'play_description.tv.decoder_only', $product);
			} else {
				$data['application_only'] = self::translatePlayDescription( 'play_description.tv.application_only', $product);
			}
			return [
				'details' => implode(', ', $data),
				'description' => Hash::get($product, 'play_description.tv.price_description.'.$this->language),
				'label' => 'TV'
			];
		} else {
			return false;
		}
	}

	/**
	 * @param $playDescriptionPath in $product array
	 * @param $product
	 * @param string $version. 'front' or 'cake', to get the domain name used in translation keys
	 * @param null $responsive
	 *
	 * @return null|string $description
	 */
	public function translatePlayDescription($playDescriptionPath, $product) {
		$description = Hash::get($product, $playDescriptionPath, null);
		if (!$description) {
			return null;
		}
		if($this->version == 'front') {
			$translation_domain = 'product';
		} else {
			$translation_domain = 'ProductAstelBe';
		}

		switch ($playDescriptionPath) {

			// MOBILE
			case 'play_description.mobile.included_minutes_calls' :
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain, 'included_minutes_calls_unlimited', $this->version);
				} else {
					return self::getTranslation($translation_domain, 'included_minutes_calls', $this->version, $description);
				};
				break;
			case 'play_description.mobile.included_data_volume' :
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain,'included_data_volume_unlimited', $this->version);
				} else {
					return self::getTranslation($translation_domain,'included_data_volume', $this->version,$description / 1000);
				}
			case 'play_description.mobile.included_sms' :
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain, 'included_sms_unlimited', $this->version);
				} else {
					return self::getTranslation($translation_domain, 'included_sms', $this->version, $description);
				}
			// INTERNET
			case 'play_description.internet.bandwidth_download' :
				return self::getTranslation($translation_domain, 'bandwidth_download', $this->version, $description);
			case 'play_description.internet.bandwidth_upload' :
				return self::getTranslation($translation_domain, 'bandwidth_upload', $this->version, $description);
			case 'play_description.internet.bandwidth_volume' :
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain, 'bandwidth_volume_unlimited', $this->version);
				} else {
					return self::getTranslation($translation_domain, 'bandwidth_volume', $this->version, $description);
				}
			// FIX
			case 'play_description.fix.included_minutes_calls' :
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain, 'included_minutes_calls_unlimited', $this->version);
				} else if ($description == 'EWE') {
					return self::getTranslation($translation_domain, 'included_minutes_calls_EWE', $this->version);
				} else {
					if ($description == 0) {
						return '/';
					}
					return self::getTranslation($translation_domain, 'included_minutes_calls', $this->version, $description);
				}
			// TV
			case 'play_description.tv.number_tv_channel' :
				return self::getTranslation($translation_domain, 'number_tv_channel', $this->version, $description);
			case 'play_description.tv.max_tv_channel' :
				return self::getTranslation($translation_domain, 'max_tv_channel', $this->version, $description);
			case 'play_description.tv.decoder_application' :
				return self::getTranslation($translation_domain, 'decoder_application', $this->version, $description);
			case 'play_description.tv.decoder_only':
				return self::getTranslation($translation_domain, 'decoder_only', $this->version, $description);
			case 'play_description.tv.application_only' :
				return self::getTranslation($translation_domain, 'application_only', $this->version, $description);
			default:
				return $description;
		}
	}

	static function getDisplayedProductCount($item) {
		if ($item['plays']['mobile'] != false && $item['count'] > 1) {
			return 'x&nbsp' . $item['count'];
		} else {
			return '';
		}
	}

	public function formatProductForCard($product) {
		$formatted_product = [];
		// debug(Hash::get($product, 'brand_logo'));
		// Product main info
		$formatted_product['count'] = Hash::get($product, 'count');
		$formatted_product['short_name'] = Hash::get($product, 'short_name.' . $this->language, '');
		$formatted_product['brand_name'] = Hash::get($product, 'brand_name');
		$formatted_product['brand_slug'] = Hash::get($product, 'brand_slug');
		$formatted_product['brand_logo'] = Hash::get($product, 'brand.fact_sheet.logo.small');
		$formatted_product['product_sheet_url'] = Hash::get($product, 'web.product_sheet_url.' .  $this->language, '');
		// Product play details
		$formatted_product['plays']['internet'] = $this->getInternetDetails($product);
		$formatted_product['plays']['tv'] = $this->getTVDetails($product);
		$formatted_product['plays']['fix'] = $this->getFixDetails($product);
		$formatted_product['plays']['mobile'] = $this->getGsmDetails($product);

		return $formatted_product;
	}



}

