<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class PartnerBlacklistCheck extends APIModel {

  protected $disableCache = true;

  public function check(array $params = []) {
    $query = $this->newQuery();
    // $query->addGETParams($params);
    $query->setUrl('v2_00/PartnerBlacklistCheck');
    $query->addPOSTParams($params);
    return $query->exec();
  }
}
