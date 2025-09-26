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
      $out .= '<link rel="stylesheet" href="' . $css . '">';
    }

    return $out;
  }

  public function getJS() {
    $out = '';
    $jsList = $this->getJSList();
    foreach ($jsList as $js) {
      $out .= '<script src="' . $js . '"></script>';
    }

    return $out;
  }

  public function getScriptLoadHardwareSelect($brand_slug = null, $view = null, $customOptions = null, $defer = false) {
    global $_GET;

    $params = [];
    $params['is_professional'] = $this->context->getIsProfessional();
    if ($brand_slug !== null) {
      $params['brand_slug'] = $brand_slug;
    }
    if ($view !== null) {
      $params['view'] = $view;
    }

    // Handle custom options for news_view context
    if (is_array($customOptions)) {
      if (isset($customOptions['context'])) {
        $params['context'] = $customOptions['context'];
      }
      if (isset($customOptions['title'])) {
        $params['custom_title'] = $customOptions['title'];
      }
      if (isset($customOptions['hideFilter'])) {
        $params['hide_filter'] = $customOptions['hideFilter'];
      }
      if (isset($customOptions['hideTitle'])) {
        $params['hide_title'] = $customOptions['hideTitle'];
      }
      // For backward compatibility, if customOptions is string (encryptionKey)
      if (is_string($customOptions)) {
        $encryptionKey = $customOptions;
      }
    } elseif (is_string($customOptions)) {
      // Backward compatibility: third parameter was encryptionKey
      $encryptionKey = $customOptions;
    }

    $params['session_id'] = $this->context->getSessionID();
    $params['page_url'] = $this->getPageURL();

    $username = Hash::get($_GET, 'username');
    if ($username !== null) {
      $params['username'] = $username;
    }

    $overridePartnerId = Hash::get($_GET, 'partnerID');
    if ($overridePartnerId !== null) {
      $params['data']['override_partner_id'] = $overridePartnerId;
    }

    // encrypt params
    if (empty($encryptionKey)) {
      $encryptionKey = $this->context->getEncryptionKey();
    }
    $getParamsStr = json_encode($params);

    $encryptedGetParams = EncryptData::encrypt($getParamsStr, $encryptionKey);

    $scriptTag = '<script';
    if ($defer) {
      $scriptTag .= ' defer';
    }
    $scriptTag .= '>
			getHardwareSelect("hardwareDiv", "' . $this->context->getLanguage() . '", "' . $encryptedGetParams . '");
		</script>';

    return $scriptTag;
  }

  public function getScriptLoadHardwareDisplay($hardware_slug, $hardware_id = null, $hardwareIndexUrl = false, $offers_brand = null, $encryptionKey = null) {
    global $_GET;
    $username = Hash::get($_GET, 'username');

    $override_partner_id = Hash::get($_GET, 'partnerID');
    $params = [
      'slug'             => $hardware_slug,
      'id'               => $hardware_id,
      'hardwareIndexUrl' => $hardwareIndexUrl,
      'offers_brand'     => $offers_brand,
      'is_professional'  => $this->context->getIsProfessional(),
      'session_id'       => $this->context->getSessionID(),
      'page_url'         => $this->getPageURL(),
      'username'         => $username,
      'partnerID'        => $override_partner_id,
    ];
    $params['selected_tab'] = Hash::get($_GET, 'selectedTab');

    // encrypt params
    if (empty($encryptionKey)) {
      $encryptionKey = $this->context->getEncryptionKey();
    }
    $getParamsStr = json_encode($params);
    $encryptedGetParams = EncryptData::encrypt($getParamsStr, $encryptionKey);

    return '<script>
			getHardwareDisplay("hardwareDiv", "' . $this->context->getLanguage() . '", "' . $encryptedGetParams . '");
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
    // For a lifetime promo, we calculate only on 24 months
    if ($product['discounted_price'] > 0 && $product['discounted_price_period'] == 0) {
      $product['discounted_price_period'] = $discounted_price_period_in_month;
    }
    $savings += ($product['price'] - $product['discounted_price']) * $product['discounted_price_period'];
    // Note: If no promo, product has discounted_price at 0 and duration at 0, as it multiply by 0 it still 0
    // Cashback (product need 'commission' embedded)
    $savings += Hash::get($product, 'commission.cashback_amount', 0);
    // debug(Hash::get($product, 'commission.cashback_amount', 0));
    return $savings;
  }
}
