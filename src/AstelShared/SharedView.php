<?php

namespace AstelShared;

use AstelSDK\Utils\Singleton;
use AstelSDK\Model\Product;
use CakeUtility\Hash;

class SharedView extends Singleton {


	public $language = 'fr';
	public $version = 'front'; // 'cake' or 'front'

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

	/**
	 * @param $product
	 * Process plays details for listings_cards
	 */
	public function getPlayDetails($item, $version = 'front', $language = 'fr') {
		$tv_content = '<span style="font-size: 1.25rem; color:#1f438c!important;">
												<strong>' . $item['tv']['number_tv_channel'] . '</strong>
										</span>';
		$tv_content .= '<span class="mr-1">';
		foreach (['decoder_application', 'decoder_only', 'application_only'] as $decoder_type) {
			if ($item['tv'][$decoder_type]) {
				$tv_content .= $item['tv'][$decoder_type];
			}
		}
		$tv_content .= '</span>';


		$details = [
			'mobile' => [
				'key' => 'mobile',
				'title' => 'GSM',
				'description' => $item['mobile']['price_description'],
				'content' => $this->getGsmDetails($item, $version, $language),
//				'content' => '<span>
//					<strong style="font-size: 1.25rem; color:#1f438c!important;">' . $item['mobile']['included_data_volume'] . '</strong>
//						</span>
//						<span class="mr-1">
//										' . $item['mobile']['included_minutes_calls'] . '
//						</span>
//						<span class="mr-1">
//										' . $item['mobile']['included_sms'] . '
//						</span>',
			],
			'internet' => [
				'key' => 'internet',
				'title' => 'NET',
				'description' => $item['internet']['price_description'] . '<br>',
				'content' => '<span>Vitesse <strong class="mt-n1" style="font-size: 1.25rem; color:#1f438c!important;">' . $item['internet']['bandwidth_download'] . '</strong>
                                                                            <span class="mr-1">' . $item['internet']['bandwidth_volume'] . '</span>
                                                                        </span>',
			],
			'tv' => [
				'key' => 'tv',
				'title' => 'TV',
				'description' => $item['tv']['price_description'],
				'content' => $tv_content,
			],
			'fix' => [
				'key' => 'fix',
				'title' => 'TEL',
				'description' => $item['fixed']['price_description'],
				'content' => '<span>' . $item['fix']['included_minutes_calls'] . '</span>',
			],
		];

		return $details;
	}

	public function getGsmDetails ($product) {
		$Product = Product::getInstance();
		if ($Product->isType($product, 'M')) {
			$details = [];
			$details[] = $this->translatePlayDescription('play_description.mobile.included_data_volume', $product);
			$details[] = $this->translatePlayDescription('play_description.mobile.included_sms', $product);
			$details[] = $this->translatePlayDescription('play_description.mobile.included_minutes_calls', $product);
			$return_details = implode(', ', $details);
			return [
				'details' => $return_details,
				'descriptions' => Hash::get($product, 'play_description.mobile.price_description.'.$this->language),
				'label' => 'GSM'
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
			$translation_domain = 'OrderAstelBe';
		}

		switch ($playDescriptionPath) {

			// MOBILE
			case 'play_description.mobile.included_minutes_calls' :
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain, 'tab_mobile_unlimited_call', $this->version);
				} else {
					return self::getTranslation($translation_domain, 'tab_mobile_minutes', $this->version, $description);
				};
				break;
			case 'play_description.mobile.included_data_volume' :
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain,'tab_mobile_unlimited_internet', $this->version);
				} else {
					return self::getTranslation($translation_domain,'tab_mobile_gb_data', $this->version,$description / 1000);
				}
				break;
			case 'play_description.mobile.included_sms' :
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain, 'tab_mobile_unlimited_sms', $this->version);
				} else {
					return self::getTranslation($translation_domain, 'tab_mobile_sms', $this->version, $description);
				}
				break;
			// INTERNET
			case 'play_description.internet.bandwidth_download' :
				return self::getTranslation($translation_domain, 'tab_internet_mbps', $this->version, $description);
				break;
			case 'play_description.internet.bandwidth_upload' :
				return self::getTranslation($translation_domain, 'tab_internet_mbps', $this->version, $description);
				break;
			case 'play_description.internet.bandwidth_volume' :
				if ($description == 'UNLIMITED') {
					return self::getTranslation($translation_domain, 'tab_unlimited', $this->version);
				} else {
					return self::getTranslation($translation_domain, 'tab_mobile_gb_data', $this->version, $description);
				}
			// FIX
			case 'play_description.fix.included_minutes_calls' :
				if ($description == 'UNLIMITED') {
//					return d__('product', 'tab_mobile_unlimited_call');
					return self::getTranslation($translation_domain, 'tab_mobile_unlimited_call', $this->version);
				} else if ($description == 'EWE') {
//					return d__('product', 'tab_fixe_EWE');
					return self::getTranslation($translation_domain, 'tab_fixe_EWE', $this->version);
				} else {
					if ($description == 0) {
						return '/';
					}
//					return d__('product', 'tab_mobile_minutes' . $responsive, $description);
					return self::getTranslation($translation_domain, 'tab_mobile_minutes', $this->version, $description);
				}
			// TV : info has no translation keys with data injected
			case 'play_description.tv.number_tv_channel' :
				return self::getTranslation($translation_domain, 'tab_tv_number_of_channel', $this->version, $description);
				break;
			case 'play_description.tv.max_tv_channel' :
				return self::getTranslation($translation_domain, 'tab_max_tv_channel', $this->version, $description);
				break;
			case 'play_description.tv.decoder_application' :
				return self::getTranslation($translation_domain, 'tab_tv_decoder_application', $this->version, $description);
				break;
			case 'play_description.tv.tab_tv_decoder_only':
				return self::getTranslation($translation_domain, 'tab_tv_decoder_only', $this->version, $description);
				break;
			case 'play_description.tv.application_only' :
				return self::getTranslation($translation_domain, 'tab_tv_application_without_decoder', $this->version, $description);
				break;
			default:
				return $description;
		}
	}

}

