<?php

namespace AstelSDK;

class PostalCodeProduct extends QueryManager {
	
	public function isProductAvailableForPostalCode($postal_code, $productID, $retrieveCityName = 1) {
		$this->init();
		$url = '/postal_code/product/available/' . $postal_code . '/' . $productID . '/' . $retrieveCityName;
		$this->setUrl($url);
		
		return $this->exec(false);
		
	}
}