<?php

namespace AstelSDK\Model;

class PostalCode extends SDKModel {
	
	protected $associated_instance_name = '\AstelSDK\API\PostalCode';
	
	public function isPostalCode($search) {
		$search = str_replace(' ', '', $search);
		if (strlen($search) === 4 && is_numeric($search)) {
			$postalCodes = $this->find('all', ['postal_code' => $search]);
			
			return !empty($postalCodes);
		}
		
		return false;
	}
}