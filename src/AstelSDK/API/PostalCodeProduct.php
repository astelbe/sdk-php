<?php

namespace AstelSDK\API;

use AstelSDK\APIModel;

class PostalCodeProduct extends APIModel {
	
	public function isProductAvailableForPostalCode($postal_code, $productID, $retrieveCityName = 1) {
		$query = $this->newQuery();
		$query->setUrl('v2_00/postal_code/product/available/' . $postal_code . '/' . $productID . '/' . $retrieveCityName);
		
		return $this->exec();
		
	}
}