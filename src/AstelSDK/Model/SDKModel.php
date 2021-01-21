<?php

namespace AstelSDK\Model;

use AstelSDK\Utils\SingletonAssociated;
use CakeUtility\Hash;

abstract class SDKModel extends SingletonAssociated {
	
	public function combineCollectionIDArrayKey(array $items) {
		$out = [];
		
		foreach ($items as $item) {
			$idItem = Hash::get($item, 'id');
			$out[$idItem] = $item;
		}
		
		return $out;
	}
	
	public function getValidationObject() {
		$response = $this->getLastFullResponseObject();
		$data = $response->getResultDataAccordingFindType('first');
		if ($data === null || empty($data)) {
			$data = [];
		}
		$parameters = Hash::get($data, 'parameters', []);
		$errors = Hash::get($data, 'parameters_in_error', []);
		$warnings = Hash::get($data, 'parameters_in_warning', []);
		$validationObject = new Validation($errors, $warnings, $data, $parameters);
		
		return $validationObject;
	}
}