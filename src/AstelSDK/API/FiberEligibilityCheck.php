<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class FiberEligibilityCheck extends APIModel {
	
  protected $disableCache = true;

  /**
   * Method to send POST data to check fiber eligibility form proximus api
   * params needed : 
   * - street1 (same name as backend order form...)
   * - street_number
   * - postal_code
   * - city
   */
  public function check( array $params = []) {
      $query = $this->newQuery();
		  $query->setUrl('v2_00/fiber_eligibility_check');
		  $query->addPOSTParams($params);
		  return $query->exec();
  }
}