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

	public function setLanguage($language) {
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
		if ($version == 'front') {
			$translation_domain = 'product';
		} else {
			$translation_domain = 'OrderAstelBe';
		}

		switch ($playDescriptionPath) {

				// MOBILE
			case 'play_description.mobile.included_minutes_calls':
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain, 'tab_mobile_unlimited_call', $version);
				} else {
					return self::getTranslation($translation_domain, 'tab_mobile_minutes' . $responsive, $version, $description);
				};
				break;
			case 'play_description.mobile.included_data_volume':
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain, 'tab_mobile_unlimited_internet', $version);
				} else {
					return self::getTranslation($translation_domain, 'tab_mobile_gb_data' . $responsive, $version, $description / 1000);
				}
				break;
			case 'play_description.mobile.included_sms':
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain, 'tab_mobile_unlimited_sms', $version);
				} else {
					return self::getTranslation($translation_domain, 'tab_mobile_sms', $version, $description);
				}
				break;
				// INTERNET
			case 'play_description.internet.bandwidth_download':
				return self::getTranslation($translation_domain, 'tab_internet_mbps', $version, $description);
				break;
			case 'play_description.internet.bandwidth_upload':
				return self::getTranslation($translation_domain, 'tab_internet_mbps', $version, $description);
				break;
			case 'play_description.internet.bandwidth_volume':
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain, 'tab_unlimited', $version);
				} else {
					return self::getTranslation($translation_domain, 'tab_mobile_gb_data', $version, $description);
				}
				// FIX
			case 'play_description.fix.included_minutes_calls':
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
			case 'play_description.tv.number_tv_channel':
				return self::getTranslation($translation_domain, 'tab_tv_number_of_channel', $version, $description);
				break;
			case 'play_description.tv.max_tv_channel':
				return self::getTranslation($translation_domain, 'tab_max_tv_channel', $version, $description);
				break;
			case 'play_description.tv.decoder_application':
				return self::getTranslation($translation_domain, 'tab_tv_decoder_application', $version, $description);
				break;
			case 'play_description.tv.tab_tv_decoder_only':
				return self::getTranslation($translation_domain, 'tab_tv_decoder_only', $version, $description);
				break;
			case 'play_description.tv.application_only':
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
			$data['included_data_volume'] = self::getTranslatedPlayDescription(
				'play_description.mobile.included_data_volume',
				$product,
				$version
			);
			$data['included_sms'] = self::getTranslatedPlayDescription(
				'play_description.mobile.included_sms',
				$product,
				$version
			);
			$data['included_minutes_calls'] = self::getTranslatedPlayDescription(
				'play_description.mobile.included_minutes_calls',
				$product,
				$version
			);
			$data['price_description'] = '<p class="sub-details-infos toggle-details toggle-details-' . $key . '">' . Hash::get($product, 'play_description.mobile.price_description.' . $language) . '</p>';
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
			$data['bandwidth_download'] = self::getTranslatedPlayDescription('play_description.internet.bandwidth_download', $product, $version);
			$data['bandwidth_volume'] = self::getTranslatedPlayDescription('play_description.internet.bandwidth_volume', $product, $version);
			$data['bandwidth_upload'] = self::getTranslatedPlayDescription('play_description.internet.bandwidth_upload', $product, $version);
			// $data['is_wifi_modem_provided'] = self::getTranslatedPlayDescription( 'play_description.internet.is_wifi_modem_provided', $product, $version);
			$is_wifi_modem_provided = Hash::get($product, 'play_description.internet.is_wifi_modem_provided', 0);
			$data['price_description'] = '<p class="sub-details-infos toggle-details toggle-details-' . $key . '">' . Hash::get($product, 'play_description.internet.price_description.' . $language) . '</p>';
			return $data;
		} else {
			return false;
		}
	}
	// TODO deprecated with getTVDetails
	static function getTVData($product, $version = 'front', $language = 'fr') {
		$Product = Product::getInstance();
		if ($Product->isType($product, 'T')) {
			$data = [];
			//			$max_tv_channel = Hash::get($product, 'play_description.tv.size_number_tv_max');
			$decoderApplication = Hash::get($product, 'play_description.tv.decoder_application');
			$data['number_tv_channel'] = self::getTranslatedPlayDescription('play_description.tv.number_tv_channel', $product, $version);
			if (Hash::get($product, 'play_description.tv.decoder_application')) {
				$data['decoder_application'] = self::getTranslatedPlayDescription('play_description.tv.decoder_application', $product, $version, $decoderApplication);
			} elseif (Hash::get($product, 'play_description.tv.decoder_only')) {
				//				$data['decoder_only'] = self::getTranslatedPlayDescription( 'play_description.tv.decoder_only', $product, $version, $decoderApplication);
				$data['decoder_only'] = "<span> Décodeur (max 1) (pas d'application TV) </span>";
			} else {
				$data['application_only'] = self::getTranslatedPlayDescription('play_description.tv.application_only', $product, $version, $decoderApplication);
			}
			$data['price_description'] = '<p class="sub-details-infos toggle-details toggle-details-' . $key . '">' . Hash::get($product, 'play_description.tv.price_description.' . $language) . '</p>';
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
			$data['included_minutes_calls'] = self::getTranslatedPlayDescription('play_description.fix.included_minutes_calls', $product, $version);
			$data['price_description'] = '<p class="sub-details-infos toggle-details toggle-details-' . $key . '">' . Hash::get($product, 'play_description.fix.price_description.' . $language) . '</p>';
			return $data;
		} else {
			return false;
		}
	}


	/**
	 * @param $domain
	 * domain may be array with 'front' and 'cake' keys
	 * I.E
	 */
	static function getTranslation($domain, $key, $version, $params = []) {
		switch ($version) {
			case 'front':
				if (is_array($domain)) {
					$domain = $domain['front'];
				}
				return d__($domain, $key, $params);
				break;
			case 'cake':
				if (is_array($domain)) {
					$domain = $domain['cake'];
				}
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



	public function getGsmDetails($product) {
		$Product = Product::getInstance();
		if ($Product->isType($product, 'M')) {
			$details = [];
			$details['included_data_volume'] = $this->translatePlayDescription('play_description.mobile.included_data_volume', $product);
			$details['included_sms'] = $this->translatePlayDescription('play_description.mobile.included_sms', $product);
			$details['included_minutes_calls'] = $this->translatePlayDescription('play_description.mobile.included_minutes_calls', $product);
			return [
				'details' => '<span class="fs100 fw700 text-darkblue pr-1">GSM </span>' . implode(', ', $details),
				'description' => Hash::get($product, 'play_description.mobile.price_description.' . $this->language),
				'label' =>
				'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="30" viewBox="0 0 20 30" fill="none">
							<rect x="1.70435" y="1.37024" width="16.5926" height="27.2593" rx="1.77778" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke="#1F438C"/>
							<path d="M7.03784 4.33313H12.9638" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke="#1F438C"/>
							<circle cx="10.0007" cy="23.2962" r="1.77778" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke="#1F438C"/>
						</svg>',
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
			$data['bandwidth_download'] = self::translatePlayDescription('play_description.internet.bandwidth_download', $product);
			$data['bandwidth_volume'] = self::translatePlayDescription('play_description.internet.bandwidth_volume', $product);
			$extra_data = [];
			$extra_data['bandwidth_upload'] = self::translatePlayDescription('play_description.internet.bandwidth_upload', $product);
			$is_wifi_modem_provided = Hash::get($product, 'play_description.internet.is_wifi_modem_provided', 0);
			if ($is_wifi_modem_provided != 0) {
				$extra_data['is_wifi_modem_provided'] = "Modem Wi-Fi";
			}
			$extra_data_string = implode(', ', $extra_data);
			$original_description = Hash::get($product, 'play_description.internet.price_description.' . $this->language);
			$description_with_extra = $extra_data_string . '<br> ' . $original_description;
			return [
				'details' => '<span class="fs100 fw700 text-darkblue pr-1">Internet </span>' . implode(', ', $data),
				'description' => $description_with_extra,
				'label' =>
				'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="26" height="26" fill="#1F438C">
						<path d="M128 32C92.7 32 64 60.7 64 96V352h64V96H512V352h64V96c0-35.3-28.7-64-64-64H128zM19.2 384C8.6 384 0 392.6 0 403.2C0 445.6 34.4 480 76.8 480H563.2c42.4 0 76.8-34.4 76.8-76.8c0-10.6-8.6-19.2-19.2-19.2H19.2z"/>
					</svg>'
			];
		} else {
			return false;
		}
	}

	public function getFixDetails($product) {
		$Product = Product::getInstance();
		if ($Product->isType($product, 'F')) {
			return [
				'details' => '<span class="fs100 fw700 text-darkblue pr-1">' . ($this->language == 'FR' ? 'Fixe' : 'Vast') . ' </span> ' . self::translatePlayDescription('play_description.fix.included_minutes_calls', $product),
				'description' => Hash::get($product, 'play_description.fix.price_description.' . $this->language),
				'label' =>
				'<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
						<path class="fixSvg" d="M29.2937 24.44C29.2937 24.92 29.187 25.4133 28.9603 25.8933C28.7337 26.3733 28.4403 26.8266 28.0537 27.2533C27.4003 27.9733 26.6803 28.4933 25.867 28.8266C25.067 29.16 24.2003 29.3333 23.267 29.3333C21.907 29.3333 20.4537 29.0133 18.9203 28.36C17.387 27.7066 15.8537 26.8266 14.3337 25.72C12.8003 24.6 11.347 23.36 9.96033 21.9866C8.58699 20.6 7.34699 19.1466 6.24033 17.6266C5.14699 16.1066 4.26699 14.5866 3.62699 13.08C2.98699 11.56 2.66699 10.1066 2.66699 8.71996C2.66699 7.81329 2.82699 6.94663 3.14699 6.14663C3.46699 5.33329 3.97366 4.58663 4.68033 3.91996C5.53366 3.07996 6.46699 2.66663 7.45366 2.66663C7.82699 2.66663 8.20033 2.74663 8.53366 2.90663C8.88033 3.06663 9.18699 3.30663 9.42699 3.65329L12.5203 8.01329C12.7603 8.34663 12.9337 8.65329 13.0537 8.94663C13.1737 9.22663 13.2403 9.50663 13.2403 9.75996C13.2403 10.08 13.147 10.4 12.9603 10.7066C12.787 11.0133 12.5337 11.3333 12.2137 11.6533L11.2003 12.7066C11.0537 12.8533 10.987 13.0266 10.987 13.24C10.987 13.3466 11.0003 13.44 11.027 13.5466C11.067 13.6533 11.107 13.7333 11.1337 13.8133C11.3737 14.2533 11.787 14.8266 12.3737 15.52C12.9737 16.2133 13.6137 16.92 14.307 17.6266C15.027 18.3333 15.7203 18.9866 16.427 19.5866C17.1203 20.1733 17.6937 20.5733 18.147 20.8133C18.2137 20.84 18.2937 20.88 18.387 20.92C18.4937 20.96 18.6003 20.9733 18.7203 20.9733C18.947 20.9733 19.1203 20.8933 19.267 20.7466L20.2803 19.7466C20.6137 19.4133 20.9337 19.16 21.2403 19C21.547 18.8133 21.8537 18.72 22.187 18.72C22.4403 18.72 22.707 18.7733 23.0003 18.8933C23.2937 19.0133 23.6003 19.1866 23.9337 19.4133L28.347 22.5466C28.6937 22.7866 28.9337 23.0666 29.0803 23.4C29.2137 23.7333 29.2937 24.0666 29.2937 24.44Z" stroke-width="2" stroke-miterlimit="10" stroke="#1F438C"/>
					</svg>'
			];
		} else {
			return false;
		}
	}

	public function getTVDetails($product) {
		$Product = Product::getInstance();
		if ($Product->isType($product, 'T')) {
			$data = [];
			$data['number_tv_channel'] = self::translatePlayDescription('play_description.tv.number_tv_channel', $product);
			if (Hash::get($product, 'play_description.tv.decoder_application')) {
				// Replace boolean decoder_only by the number of tv max for translation
				$product['play_description']['tv']['decoder_application'] = Hash::get($product, 'play_description.tv.size_number_tv_max');
				$data['decoder_application'] = self::translatePlayDescription('play_description.tv.decoder_application', $product);
			} elseif (Hash::get($product, 'play_description.tv.decoder_only')) {
				// Replace boolean decoder_only by the number of tv max for translation
				$product['play_description']['tv']['decoder_only'] = Hash::get($product, 'play_description.tv.size_number_tv_max');
				$data['decoder_only'] = self::translatePlayDescription('play_description.tv.decoder_only', $product);
			} else {
				$data['application_only'] = self::translatePlayDescription('play_description.tv.application_only', $product);
			}
			return [
				'details' => '<span class="fs100 fw700 text-darkblue pr-1">TV</span> ' . implode(', ', $data),
				'description' => Hash::get($product, 'play_description.tv.price_description.' . $this->language),
				'label' =>
					'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="26" height="26" fill="#1F438C">
						<path d="M64 64V352H576V64H64zM0 64C0 28.7 28.7 0 64 0H576c35.3 0 64 28.7 64 64V352c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V64zM128 448H512c17.7 0 32 14.3 32 32s-14.3 32-32 32H128c-17.7 0-32-14.3-32-32s14.3-32 32-32z"/>
					</svg>'
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
		if ($this->version == 'front') {
			$translation_domain = 'product';
		} else {
			$translation_domain = 'ProductAstelBe';
		}

		switch ($playDescriptionPath) {

				// MOBILE
			case 'play_description.mobile.included_minutes_calls':
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain, 'included_minutes_calls_unlimited', $this->version);
				} else {
					return self::getTranslation($translation_domain, 'included_minutes_calls', $this->version, $description);
				};
				break;
			case 'play_description.mobile.included_data_volume':
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain, 'included_data_volume_unlimited', $this->version);
				} else {
					return self::getTranslation($translation_domain, 'included_data_volume', $this->version, $description / 1000);
				}
			case 'play_description.mobile.included_sms':
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain, 'included_sms_unlimited', $this->version);
				} else {
					return self::getTranslation($translation_domain, 'included_sms', $this->version, $description);
				}
				// INTERNET
			case 'play_description.internet.bandwidth_download':
				return self::getTranslation($translation_domain, 'bandwidth_download', $this->version, $description);
			case 'play_description.internet.bandwidth_upload':
				return self::getTranslation($translation_domain, 'bandwidth_upload', $this->version, $description);
			case 'play_description.internet.bandwidth_volume':
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain, 'bandwidth_volume_unlimited', $this->version);
				} else {
					return self::getTranslation($translation_domain, 'bandwidth_volume', $this->version, $description);
				}
				// FIX
			case 'play_description.fix.included_minutes_calls':
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
			case 'play_description.tv.number_tv_channel':
				return self::getTranslation($translation_domain, 'number_tv_channel', $this->version, $description);
			case 'play_description.tv.max_tv_channel':
				return self::getTranslation($translation_domain, 'max_tv_channel', $this->version, $description);
			case 'play_description.tv.decoder_application':
				return self::getTranslation($translation_domain, 'decoder_application', $this->version, $description);
			case 'play_description.tv.decoder_only':
				return self::getTranslation($translation_domain, 'decoder_only', $this->version, $description);
			case 'play_description.tv.application_only':
				return self::getTranslation($translation_domain, 'application_only', $this->version, $description);
			default:
				return $description;
		}
	}

	static function getDisplayedProductCount($item) {
		if ($item['plays']['mobile'] != false && $item['count'] > 1) {
			return $item['count'] . '&nbspx&nbsp';
		} else {
			return '';
		}
	}

	/**
	 * Check if a result has only mobile to conditionnaly display summary stuffs
	 * @param $result card result
	 * @return bool
	 * */
	static function isOnlyMobile($result) {
		$isOnlyMobile = true;
		foreach ($result['products'] as $product) {
			// If at least one play has a description, it's not a solo mobile result
			foreach (['internet', 'tv', 'fix'] as $play) {

				if (!empty($product['plays'][$play])) {
					$isOnlyMobile = false;
				}
			}
		}
		return $isOnlyMobile;
	}

	/**
	 * Format a product to be displayed in a card
	 * @param $product
	 * @return array
	 * Used in operator page and Compare page
	 */
	public function formatProductForCard($product) {
		$formatted_product = [];

		// Product main info
		$formatted_product['count'] = Hash::get($product, 'count');
		$formatted_product['short_name'] = Hash::get($product, 'short_name.' . $this->language, '');
		$formatted_product['name'] = Hash::get($product, 'name.' . $this->language, '');
		$formatted_product['brand_name'] = Hash::get($product, 'brand_name');
		$formatted_product['brand_slug'] = Hash::get($product, 'brand_slug');
		$formatted_product['brand_logo'] = Hash::get($product, 'brand.fact_sheet.logo.small');
		$formatted_product['brand_bg_color'] = $this->getBrandColorBg(Hash::get($product, 'brand.fact_sheet.color_code'));
		$formatted_product['product_sheet_url'] = Hash::get($product, 'web.product_sheet_url.' .  $this->language, '');
		$formatted_product['brand_bg_color'] = $this->getBrandColorBg(Hash::get($product, 'brand.fact_sheet.color_code'));

		// Product play details
		$formatted_product['plays']['internet'] = $this->getInternetDetails($product);
		$formatted_product['plays']['tv'] = $this->getTVDetails($product);
		$formatted_product['plays']['fix'] = $this->getFixDetails($product);
		$formatted_product['plays']['mobile'] = $this->getGsmDetails($product);
		return $formatted_product;
	}


	/**
	 * @param $brandHexColor - hexadecimal color code of the brand (without "#")
	 * 
	 * @return $bgColor - rgba color code of the brand with 0.1 opacity
	 * 
	 * (duplicated in AstelBusinessHelper.php)
	 */
	static function getBrandColorBg($brandHexColor) {
		$bgColorR = hexdec(substr($brandHexColor, 0, 2));
		$bgColorG = hexdec(substr($brandHexColor, 2, 2));
		$bgColorB = hexdec(substr($brandHexColor, 4, 2));

		$bgColor = "rgba(" . $bgColorR . ", " . $bgColorG . ", " . $bgColorB . ", 0.1)";

		return $bgColor;
	}
}
