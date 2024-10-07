<?php

namespace AstelShared;

use AstelSDK\Utils\Singleton;
use AstelSDK\Model\Product;
use CakeUtility\Hash;
use AstelSDK\AstelContext;
use AstelShared\Translate\Translate;
use AstelSDK\Utils\VatCalculation;



class SharedView extends Singleton {

  private $translator;
  public $language = 'FR';
  public $version = 'front'; // 'front' or 'cake', to get the domain name used in translation keys

  public function __construct() {
    // $this->language = AstelContext::getInstance()->getLanguage();
  }

  public function render($path, $params = []) {
    include __DIR__ . '/../AstelShared/View/' . $path . '.php';
  }

  public function setLanguage($language) {
    $this->language = $language;
    // We also need to set the language in the context to get the right translations
    // because issue with COMP : we loose language and get back to FR by default
    AstelContext::getInstance()->setLanguage($language);
  }

  public function getLanguage() {
    return $this->language;
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
      $html .= '<i class="fa fa-star fa-lg"></i>';
      $s++;
    }
    $halfStars = ceil($quality) - $fullStars;
    $s = 0;
    while ($s < $halfStars) {
      $html .= '<i class="fa fa-star-half-o fa-lg"></i>';
      $s++;
    }
    $emptyStats = 5 - $fullStars - $halfStars;
    $s = 0;
    while ($s < $emptyStats) {
      $html .= '<i class="fa fa-star-o fa-lg"></i>';
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
        'details'     => '<span class="fs100 fw700 text-darkblue pr-1">GSM </span>' . implode(', ', $details),
        'description' => Hash::get($product, 'play_description.mobile.price_description.' . $this->language),
        'label'       =>
        '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="30" viewBox="0 0 20 30" fill="none">
							<rect x="1.70435" y="1.37024" width="16.5926" height="27.2593" rx="1.77778" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke="#1F438C"/>
							<path d="M7.03784 4.33313H12.9638" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke="#1F438C"/>
							<circle cx="10.0007" cy="23.2962" r="1.77778" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke="#1F438C"/>
						</svg>',
        'count'       => $product['count'],
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
        'details'     => '<span class="fs100 fw700 text-darkblue pr-1">Internet </span>' . implode(', ', $data),
        'description' => $description_with_extra,
        'label'       =>
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="26" height="26" fill="#1F438C">
						<path d="M128 32C92.7 32 64 60.7 64 96V352h64V96H512V352h64V96c0-35.3-28.7-64-64-64H128zM19.2 384C8.6 384 0 392.6 0 403.2C0 445.6 34.4 480 76.8 480H563.2c42.4 0 76.8-34.4 76.8-76.8c0-10.6-8.6-19.2-19.2-19.2H19.2z"/>
					</svg>',
      ];
    } else {
      return false;
    }
  }

  public function getFixDetails($product) {
    $Product = Product::getInstance();
    if ($Product->isType($product, 'F')) {
      return [
        'details'     => '<span class="fs100 fw700 text-darkblue pr-1">' . ($this->language == 'FR' ? 'Fixe' : 'Vast') . ' </span> ' . self::translatePlayDescription('play_description.fix.included_minutes_calls', $product),
        'description' => Hash::get($product, 'play_description.fix.price_description.' . $this->language),
        'label'       =>
        '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
						<path class="fixSvg" d="M29.2937 24.44C29.2937 24.92 29.187 25.4133 28.9603 25.8933C28.7337 26.3733 28.4403 26.8266 28.0537 27.2533C27.4003 27.9733 26.6803 28.4933 25.867 28.8266C25.067 29.16 24.2003 29.3333 23.267 29.3333C21.907 29.3333 20.4537 29.0133 18.9203 28.36C17.387 27.7066 15.8537 26.8266 14.3337 25.72C12.8003 24.6 11.347 23.36 9.96033 21.9866C8.58699 20.6 7.34699 19.1466 6.24033 17.6266C5.14699 16.1066 4.26699 14.5866 3.62699 13.08C2.98699 11.56 2.66699 10.1066 2.66699 8.71996C2.66699 7.81329 2.82699 6.94663 3.14699 6.14663C3.46699 5.33329 3.97366 4.58663 4.68033 3.91996C5.53366 3.07996 6.46699 2.66663 7.45366 2.66663C7.82699 2.66663 8.20033 2.74663 8.53366 2.90663C8.88033 3.06663 9.18699 3.30663 9.42699 3.65329L12.5203 8.01329C12.7603 8.34663 12.9337 8.65329 13.0537 8.94663C13.1737 9.22663 13.2403 9.50663 13.2403 9.75996C13.2403 10.08 13.147 10.4 12.9603 10.7066C12.787 11.0133 12.5337 11.3333 12.2137 11.6533L11.2003 12.7066C11.0537 12.8533 10.987 13.0266 10.987 13.24C10.987 13.3466 11.0003 13.44 11.027 13.5466C11.067 13.6533 11.107 13.7333 11.1337 13.8133C11.3737 14.2533 11.787 14.8266 12.3737 15.52C12.9737 16.2133 13.6137 16.92 14.307 17.6266C15.027 18.3333 15.7203 18.9866 16.427 19.5866C17.1203 20.1733 17.6937 20.5733 18.147 20.8133C18.2137 20.84 18.2937 20.88 18.387 20.92C18.4937 20.96 18.6003 20.9733 18.7203 20.9733C18.947 20.9733 19.1203 20.8933 19.267 20.7466L20.2803 19.7466C20.6137 19.4133 20.9337 19.16 21.2403 19C21.547 18.8133 21.8537 18.72 22.187 18.72C22.4403 18.72 22.707 18.7733 23.0003 18.8933C23.2937 19.0133 23.6003 19.1866 23.9337 19.4133L28.347 22.5466C28.6937 22.7866 28.9337 23.0666 29.0803 23.4C29.2137 23.7333 29.2937 24.0666 29.2937 24.44Z" stroke-width="2" stroke-miterlimit="10" stroke="#1F438C"/>
					</svg>',
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
        'details'     => '<span class="fs100 fw700 text-darkblue pr-1">TV</span> ' . implode(', ', $data),
        'description' => Hash::get($product, 'play_description.tv.price_description.' . $this->language),
        'label'       =>
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="26" height="26" fill="#1F438C">
						<path d="M64 64V352H576V64H64zM0 64C0 28.7 28.7 0 64 0H576c35.3 0 64 28.7 64 64V352c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V64zM128 448H512c17.7 0 32 14.3 32 32s-14.3 32-32 32H128c-17.7 0-32-14.3-32-32s14.3-32 32-32z"/>
					</svg>',
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
          // return self::getTranslation($translation_domain, 'included_minutes_calls_unlimited', $this->version);
          return Translate::get('included_minutes_calls_unlimited');
        } else {
          // return self::getTranslation($translation_domain, 'included_minutes_calls', $this->version, $description);
          return Translate::get('included_minutes_calls', $description);
        };
        break;
      case 'play_description.mobile.included_data_volume':
        if ($description == 'UNLIMITED') {
          // return self::getTranslation($translation_domain, 'included_data_volume_unlimited', $this->version);
          return Translate::get('included_data_volume_unlimited');
        } else {
          // return self::getTranslation($translation_domain, 'included_data_volume', $this->version, $description / 1000);
          return Translate::get('included_data_volume', $description / 1000);
        }
      case 'play_description.mobile.included_sms':
        if ($description == 'UNLIMITED') {
          // return self::getTranslation($translation_domain, 'included_sms_unlimited', $this->version);
          return Translate::get('included_sms_unlimited');
        } else {
          // return self::getTranslation($translation_domain, 'included_sms', $this->version, $description);
          return Translate::get('included_sms', $description);
        }
        // INTERNET
      case 'play_description.internet.bandwidth_download':
        // return self::getTranslation($translation_domain, 'bandwidth_download', $this->version, $description);
        return Translate::get('bandwidth_download', $description);
      case 'play_description.internet.bandwidth_upload':
        // return self::getTranslation($translation_domain, 'bandwidth_upload', $this->version, $description);
        return Translate::get('bandwidth_upload', $description);
      case 'play_description.internet.bandwidth_volume':
        if ($description == 'UNLIMITED') {
          // return self::getTranslation($translation_domain, 'bandwidth_volume_unlimited', $this->version);
          return Translate::get('bandwidth_volume_unlimited');
        } else {
          // return self::getTranslation($translation_domain, 'bandwidth_volume', $this->version, $description);
          return Translate::get('bandwidth_volume', $description);
        }
        // FIX
      case 'play_description.fix.included_minutes_calls':
        if ($description == 'UNLIMITED') {
          // return self::getTranslation($translation_domain, 'included_minutes_calls_unlimited', $this->version);
          return Translate::get('included_minutes_calls_unlimited');
        } else if ($description == 'EWE') {
          // return self::getTranslation($translation_domain, 'included_minutes_calls_EWE', $this->version);
          return Translate::get('included_minutes_calls_EWE');
        } else {
          if ($description == 0) {
            return '/';
          }
          // return self::getTranslation($translation_domain, 'included_minutes_calls', $this->version, $description);
          return Translate::get('included_minutes_calls', $description);
        }
        // TV
      case 'play_description.tv.number_tv_channel':
        // return self::getTranslation($translation_domain, 'number_tv_channel', $this->version, $description);
        return Translate::get('number_tv_channel', $description);
      case 'play_description.tv.max_tv_channel':
        // return self::getTranslation($translation_domain, 'max_tv_channel', $this->version, $description);
        return Translate::get('max_tv_channel', $description);
      case 'play_description.tv.decoder_application':
        // return self::getTranslation($translation_domain, 'decoder_application', $this->version, $description);
        return Translate::get('decoder_application', $description);
      case 'play_description.tv.decoder_only':
        // return self::getTranslation($translation_domain, 'decoder_only', $this->version, $description);
        return Translate::get('decoder_only', $description);
      case 'play_description.tv.application_only':
        // return self::getTranslation($translation_domain, 'application_only', $this->version, $description);
        return Translate::get('application_only', $description);
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
    $formatted_product['id'] = Hash::get($product, 'id');
    $formatted_product['count'] = Hash::get($product, 'count');
    $formatted_product['short_name'] = Hash::get($product, 'short_name.' . $this->language, '');
    $formatted_product['name'] = Hash::get($product, 'name.' . $this->language, '');
    $formatted_product['brand_name'] = Hash::get($product, 'brand_name');
    $formatted_product['brand_slug'] = Hash::get($product, 'brand_slug');
    $formatted_product['brand_logo'] = Hash::get($product, 'brand.fact_sheet.logo.small');
    $formatted_product['product_sheet_url'] = Hash::get($product, 'web.product_sheet_url.' . $this->language, '');
    $formatted_product['brand_bg_color'] = $this->getBrandColorBg(Hash::get($product, 'brand.fact_sheet.color_code'));
    // $formatted_product['total_savings'] = $this->calculateSavings($product) ? Translate::get('total_savings', self::formatPrice($this->calculateSavings($product))) : null;

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

  /**
   * @param $product
   * @param $getOnlyActivationOrInstallation must be 'activation' or 'installation'. Used to get only one of those prices
   * @return string html of prices to be displayed
   */
  static function getProductActivationAndOrInstallationPrice($product, $getOnlyActivationOrInstallation = '', $options = []) {
    // Get and calculate prices
    if (empty($product)) {
      return '';
    }
    $activation_fee = Hash::get($product, 'activation_fee', false);
    $installation_fee = Hash::get($product, 'installation_fee', false);
    $activation_fee_reduced = Hash::get($product, 'activation_fee_reduced', 0);
    $installation_fee_reduced = Hash::get($product, 'installation_fee_reduced', 0);
    $setup_fee_reduced = $activation_fee_reduced + $installation_fee_reduced;
    if ($activation_fee || $installation_fee) {
      $setup_fee = $activation_fee + $installation_fee;
    } else {
      $setup_fee = 0;
    }
   
    $css_classes = $options['css_classes'] ?: 'font-weight-bold';

    $html = '';
    switch ($getOnlyActivationOrInstallation) {
      case 'activation':
        $html .= Translate::get('activation_price');
        $html .= self::getHtmlForPriceWithPossibleReduction($activation_fee, $activation_fee_reduced, $css_classes);
        break;
      case 'installation':
        $html .= Translate::get('installation_price');
        $html .= self::getHtmlForPriceWithPossibleReduction($installation_fee, $installation_fee_reduced, $css_classes);
        break;
      default:
        // Get both. From comparator, is_solo_mobile
        $is_solo_mobile = (!empty($product['is_mobile']) && empty($product['is_fix']) && empty($product['is_internet']) && empty($product['is_tv'])) || $product['is_solo_mobile'] == true;
        if ($is_solo_mobile) {
          $html .= Translate::get('starting_costs_mobile');
        } else {
          $html .= Translate::get('starting_costs');
        }
        $html .= self::getHtmlForPriceWithPossibleReduction($setup_fee, $setup_fee_reduced, $css_classes);
    }

    return $html;
  }

  /**
   * @param $full_price
   * @param $reduced_price
   * @return string
   * Return html of price to be displayed. Add del full price. Display 'free' if price is 0
   */
  static function getHtmlForPriceWithPossibleReduction($full_price, $reduced_price, $css_classes = '') {
    $html = '';
        if ($full_price > $reduced_price) {
      $html .= '<del class="pr-2 text-crossedgrey">' . self::formatPrice($full_price) . '</del>';
      if ($reduced_price > 0) {
        $html .= '<span class="' . $css_classes . '">' . self::formatPrice($reduced_price) . '</span>';
      } else {
        $html .= '<span class="text-astelpink ' . $css_classes . '">' . Translate::get('free') . '</span>';
      }
    } else {
      if ($full_price > 0) {
        $html .= '<span class="' . $css_classes . '">' . self::formatPrice($full_price) . '</span>';
      } else {
        $html .= '<span class="text-astelpink ' . $css_classes . '">' . Translate::get('free') . '</span>';
      }
    }
    return $html;
  }

  static function formatPrice($price, $show_free_text = '') {
    $language = AstelContext::getInstance()->getLanguage();
    $price = floatval($price);
    $intPart = intval($price);
    $floatingpart = $price - $intPart;
    if ($floatingpart == 0) {
      $price = $intPart;
    } else {
      // purpose of this condition ?
      if ($language == 'FR') {
        $price = number_format(round($price, 2), 2, ',', '.');
      } elseif ($language == 'NL') {
        $price = number_format(round($price, 2), 2, ',', '.');
      }
    }

    if ($price == 0 && $show_free_text != '') {
      return $show_free_text;
    }
    // add class around decimal
    $price = $language == 'NL' ? '<span class="currency-symbol">€</span>' . $price : $price . '&nbsp<span class="currency-symbol">€</span>';
    $exploded_price = explode(',', $price);
    if (isset($exploded_price[1])) {
      if($exploded_price[1] != '00') {
        $price = $exploded_price[0] . '<span class="decimal">' . ',' . $exploded_price[1] . '</span>';
      } else {
        $price = $exploded_price[0];
      }

    }

    return $price;
  }


  /**
   * @param $product
   * Prepare the summary of a product to be displayed in a card - Used for front, not for COMP
   */
  static function getProductResultSummary($product, $preProcessedData = [], $blockKey = 1, $productForPlugs = null) {

    if ($productForPlugs === null) {
      $productForPlugs = $product;
    }

    // Cashback
    $cashbackAmount = Hash::get($product, 'commission.cashback_amount', 0);
    if ($cashbackAmount != 0) {
      $displayed_cashback = Translate::get('product_table_content_cashback', $placeholders = 'test') . ' -' . self::formatPrice($cashbackAmount);
    } else {
      $displayed_cashback = null;
    }

    // product savings
    $savings = self::calculateSavings($product);

    $result_summary = [
      'displayed_price'        => self::getDisplayedPrice($product, ['color-css-class' => 'color-operator', 'br-before-during-month' => true]),
      'total_cashback'         => $displayed_cashback,
      'phone_plug'             => self::displayPlugList([$productForPlugs], $blockKey),
      'setup'                  => self::getProductActivationAndOrInstallationPrice($product),
      'max_activation_time'    => Translate::get('max_activation_time', [$product['brand_name'], $product['max_activation_time']]),
      'products_total_savings' => $savings > 0 ? Translate::get('total_savings', self::formatPrice($savings)) : null,
    ];

    return $result_summary;
  }


  static function getDisplayedPrice($entity, $options = []) {
    // options handling
    // 2 linebreak options, 
    // - after main price (if discount), 
    // - between duration and price after
    $linebreak_after_main_price = Hash::get($options, 'linebreak_after_main_price', true);
    $linebreak_after_duration = Hash::get($options, 'linebreak_after_duration', false);

    $pricePath = 'price';
    if (isset($options['price_path'])) {
      $pricePath = $options['price_path'];
    }

    $removeTextColor = false;
    if ($options['removeTextColor'] === true) {
      $removeTextColor = true;
    }

    $freeText = Translate::get('free');
    if (isset($options['free_text'])) {
      $freeText = $options['free_text'];
    }
    $priceType = 'MONTH';
    if (isset($options['price_type'])) {
      $priceType = $options['price_type'];
    }
    $priceTypeText = ' ' . Translate::get('per_month');
    if ($priceType === 'UNIT') {
      $priceTypeText = '';
    }

    // price calculation
    $VatCalculation = VatCalculation::getInstance();
    $websiteScopeProfessional = AstelContext::getInstance()->getIsProfessional();
    $productIsEncodedHTVA = Hash::get($entity, 'is_htva', 0);
    if ($websiteScopeProfessional) {
      $VATD = ' ' . Translate::get('EVAT'); // Show HTVA on the website
    } else {
      $VATD = ''; // No TTC displayed when private
    }

    $discountedPrice = 0;
    if ($pricePath === 'price') {
      $discountedPrice = $VatCalculation->calculatePriceForceHTVA(Hash::get($entity, 'discounted_price', 0), $productIsEncodedHTVA, $websiteScopeProfessional);
    }
    $price = $VatCalculation->calculatePriceForceHTVA(Hash::get($entity, $pricePath, 0), $productIsEncodedHTVA, $websiteScopeProfessional);
    $priceFormatted = self::formatPrice($price);

    $isDiscount = false;
    $isDuration = false;
    if ($discountedPrice > 0 && $discountedPrice !== $price) {
      $isDiscount = true;
    }
    $discountedPricePeriod = Hash::get($entity, 'discounted_price_period', 0);
    if ($discountedPricePeriod > 0) {
      $isDiscount = true;
      $isDuration = true;
    }

    $displayedPriceText = '';
    if ($isDiscount) {
      // There is a discount : "20 € while 6 month, then 30€ /month"
      // Main price 
      // "20 € / month"
      $displayedPriceText .= '<span class="' . ($removeTextColor ? '' : 'text-astelpink') . ' big-product-price"><b>' . self::formatPrice($discountedPrice) . ' </b></span>' . '<span class="' . ($removeTextColor ? '' : 'text-astelpink') . ' fs125 font-weight-bold ' . (!$linebreak_after_main_price ? 'pr-2' : '') . '">' . $priceTypeText . '</span> ';
      // linebreak
      if ($linebreak_after_main_price) {
        $displayedPriceText .= '<br>';
      }
      if ($isDuration) {
        // "while 6 month"
        $displayedPriceText .= '<span class=""> ' . Translate::get('during_months', $discountedPricePeriod) . ',</span> ';
        // linebreak
        if ($linebreak_after_duration) {
          $displayedPriceText .= '<br>';
        }
        // "then"
        $displayedPriceText .= ' <span>' . Translate::get('price_after') . '</span> ';
      }
      // "30€ /month"
      $crossPriceIfDuration = !$isDuration; // del price if discount is forever
      $displayedPriceText .= '<span class="regular-product-price font-weight-bold' . ($crossPriceIfDuration ? ' crossed' : '') . '">' . self::formatPrice($price) . '</span>' . ' ' . Translate::get('per_month');
    } else {
      // No discount : "20 €/month"
      $displayedPriceText .= '<span class="' . ($removeTextColor ? '' : 'text-astelpink') . ' big-product-price"><b>' . self::formatPrice($price) . '</b></span>' . '<span class="' . ($removeTextColor ? '' : 'text-astelpink') . ' fs094 ml-1">' . $priceTypeText . '</span>';
    }
    return $displayedPriceText;
  }

  /**
   * @param $product
   *
   * @return float
   *
   * Product savings is monthly discount + installation and activation fee discounts + cashback.
   *
   * For cashback, product must be passed with 'commission' embedded, as value comes from partner info
   */
  public function calculateSavings($product) {
        // Config default price period if promo are unlimited - we calculate promo savings only for a restricted period
    $discounted_price_period_in_month = 12;
    $savings = 0;
    // Calculate savings on setup
    $total_setup_price = Hash::get($product, 'activation_fee', 0) + Hash::get($product, 'installation_fee', 0);
    $reduced_total_setup_price = Hash::get($product, 'activation_fee_reduced', 0) + Hash::get($product, 'installation_fee_reduced', 0);
    $savings += ($total_setup_price > $reduced_total_setup_price ? $total_setup_price - $reduced_total_setup_price : 0);

    // Add price promo savings
    // For a lifetime promo, we calculate only on discounted_price_period_in_month
    if ($product['discounted_price'] > 0 && $product['discounted_price_period'] == 0) {
      $product['discounted_price_period'] = $discounted_price_period_in_month;
    }
    $savings += ($product['price'] - $product['discounted_price']) * $product['discounted_price_period'];
    // Note: If no promo, product has discounted_price at 0 and duration at 0, as it multiply by 0 it still 0

    // Cashback (product need 'commission' embedded)
    $savings += Hash::get($product, 'commission.cashback_amount', 0);
    return $savings;
  }

  /**
   * This function retrieves plug tags from a given array of products.
   * Each product can contain multiple tags, and the function organizes these tags by product ID.
   *
   * @param array $products An array containing product data, where each product can have multiple tags.
   * @return array An associative array where the keys are product IDs and the values are arrays of tags.
   */
  private function getPlugTag($products = []) {
    // Initialize an empty array to store tags by product ID
    $tags = [];

    // Iterate through each products
    foreach ($products as $product) {
      // Initialize an empty array for the current product's tags

      // Iterate through each tag (if the product has any) and add it to the product's tag array
      if (isset($product['tag'])) {
        foreach ($product['tag'] as $tag) {
          $tags[$product['id']][] = $tag;
        }
      }
    }

    // Return the associative array of tags organized by product ID
    return $tags;
  }


  /**
   * This function generates HTML content for displaying a list of plugs in a modal dialog.
   * It takes a block of plugs and a modal key as parameters.
   *
   * @param array $products An array containing products.
   * @param string $modalKey A unique key for the modal dialog.
   * @return string|null The generated HTML content or null if the block is empty.
   */
  public function displayPlugList($products = [], $modalKey) {
    $language = AstelContext::getInstance()->getLanguage();

    // Retrieve plug tags from the block
    $blockPlugs = self::getPlugTag($products);
    // debug($blockPlugs);

    // Initialize variables for modal link and modal content
    $plugsModaleLink = "";

    $plugsModale = "";

    // Check if there are any plugs in the block
    if (!empty($blockPlugs)) {
      $productsInBlockIndex = 1;

      // Iterate through each block's product
      foreach ($blockPlugs as $plugsByProduct) {
        $plugsInProductIndex = 1;

        // Add 'AND' separator if there are multiple products inside the block
        if ($productsInBlockIndex > 1 && !empty($plugsByProduct)) {
          $plugsModaleLink .= Translate::get('and') . '<br>';

          $plugsModale .=
            '<div class="m-5">
							<hr class="m-0">
							<div class="centered-axis-x">
								<span class="font-s-11 bg-white px-3 text-uppercase">' .
            strtoupper(Translate::get('and')) .
            '</span>
							</div>
						</div>';
        }

        // Iterate through each plug in the product
        foreach ($plugsByProduct as $plug) {
          // Check if the plug belongs to the specific tag group
          if ($plug['tag_group_id'] == 15) {
            // Add 'OR' separator if there are multiple plugs in the product
            if ($plugsInProductIndex > 1) {
              $plugsModaleLink .= Translate::get('or') . '<br>';

              $plugsModale .=
                '<div class="m-5">
									<hr class="m-0">
									<div class="centered-axis-x">
										<span class="font-s-11 bg-white px-3 text-uppercase">' .
                strtoupper(Translate::get('or')) .
                '</span>
									</div>
								</div>';
            }

            // Add plug details to the modal link and modal content
            $plugsModaleLink .= Hash::get($plug, 'value_translated.' . $language) . '<br>';

            $plugsModale .= '<h5 class="font-weight-bold text-black">' . Hash::get($plug, 'value_translated.' . $language) . '</h5>';
            $plugsModale .= '<p>' . Hash::get($plug, 'description_translated.' . $language) . '</p>';
            if (Hash::get($plug, 'banner_picture.' . $language, false)) {
              $plugsModale .= '<div class="text-center"><img src="' . Hash::get($plug, 'banner_picture.' . $language) . '" class="img-fluid"></div>';
            }
          }

          $plugsInProductIndex++;
        }

        $productsInBlockIndex++;
      }

      // Format the modal link and modal content
      $formattedModaleLink =
        '<p class="fs087 mt-1 mb-2 cursor-pointer noUnderline" data-toggle="modal" data-target="#modalPlugs_' . $modalKey . '">
						<span class=underlinedTitle>' . Translate::get('plug_used') . '<i class=" pl-2 fa fa-info"></i></span><br>' 
          . $plugsModaleLink .
        '</p>';

      $formattedModale =
        '<div class="modal fade" id="modalPlugs_' . $modalKey . '" tabindex="-1" role="dialog" aria-labelledby="modal' . Translate::get('plug_used') . '" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered modal-md" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">
									' . Translate::get('plug_used') . '
								</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								<div id="plug_112" class="mb-4">' . $plugsModale . '</div>
							</div>
						</div>
					</div>
				</div>';

      // Return the combined modal link and modal content
      $return = $formattedModaleLink . $formattedModale;

      return $return;
    } else {
      // Return null if there are no plugs in the block
      return null;
    }
  }
}
