<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class FiberEligibilityCheck extends APIModel {
	
  public function check( array $params = []) {

      $response = null;
  
      $query = $this->newQuery();
		  $query->setUrl('v2_00/fiber_eligibility_check');
		  $query->addGETParams($params);
		  $response = $query->exec();
      $this->handlesResponseThrows($response);
      $return = $this->returnResponse($response, $type);

      return $return;
  }
}