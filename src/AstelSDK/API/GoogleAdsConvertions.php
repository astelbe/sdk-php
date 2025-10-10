<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class GoogleAdsConvertions extends APIModel {

  protected function getAll(array $params = []) {
    $query = $this->newQuery();
    $query->setUrl('v2_00/google_ads_conversions');
    $default_params = [
      //			'accessible_as_page_in_front' => 1,
    ];

    $params = Hash::merge($default_params, $params);

    $query->addGETParams($params);

    return $query->exec();
  }
}
