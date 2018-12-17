<?php

namespace AstelSDK\API;

use AstelSDK\QueryManager;

class PostalCodeProduct extends QueryManager {
	
	public function isProductAvailableForPostalCode($postal_code, $productID, $retrieveCityName = 1) {
		$this->init();
		$url = 'v2_00/postal_code/product/available/' . $postal_code . '/' . $productID . '/' . $retrieveCityName;
		$this->setUrl($url);
		
		return $this->exec(self::RETURN_MULTIPLE_ELEMENTS);
		
	}
}