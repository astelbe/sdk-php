<?php

namespace AstelSDK\API;

use CakeUtility\Hash;
use AstelSDK\Utils\URL;

class Product extends APIModel {
	
	protected function getAll(array $params = []) {
		$query = $this->newQuery();
		$query->setUrl('v2_00/product');
		$default_params = [
			'is_visible' => 1,
		];
		if ($this->context->getIsPrivate() !== null) {
			if ($this->context->getIsPrivate()) {
				$default_params['is_private'] = 1;
			} else {
				$default_params['is_professional'] = 1;
			}
		}
		$params = Hash::merge($default_params, $params);
		if (isset($params['search_slug'])) {
			$params['search_slug'] = URL::base64url_encode($params['search_slug']);
		}
		if (isset($params['search_brand_slug'])) {
			$params['search_brand_slug'] = URL::base64url_encode($params['search_brand_slug']);
		}
		
		$query->addGETParams($params);
		
		return $query->exec();
	}
	
	protected function getFirst(array $params = []) {
		$id = Hash::get($params, 'id');
		if ($id === null || !is_numeric($id)) {
			return false;
		}
		unset($params['id']);
		$query = $this->newQuery();
		$query->setUrl('v2_00/product/' . $id);
		$query->addGETParams($params);
		
		return $query->exec();
	}
	
	public function isAvailable($product_id, $postal_code_id) {
		if ($product_id === null || !is_numeric($product_id)) {
			return false;
		}
		if ($postal_code_id === null || !is_numeric($postal_code_id)) {
			return false;
		}
		$query = $this->newQuery();
		$query->setUrl('v2_00/product/' . $product_id . '/available/' . $postal_code_id);
		$query->addGETParams(['_embed' => 'postal_code']);
		
		$response = $query->exec();
		$this->handlesResponseThrows($response);
		
		return $this->returnResponse($response, 'all');
	}
	
	public function isAvailableSearch($product_id, $searchTxt) {
		if ($product_id === null || !is_numeric($product_id)) {
			return false;
		}
		if ($searchTxt === null || $searchTxt === '' || !is_numeric($searchTxt)) {
			return false;
		}
		$query = $this->newQuery();
		$query->setUrl('v2_00/product/' . $product_id . '/available/search/' . URL::base64url_encode($searchTxt));
		$query->addGETParams(['_embed' => 'postal_code']);
		
		$response = $query->exec();
		$this->handlesResponseThrows($response);
		
		return $this->returnResponse($response, 'all');
	}

	/*
	 * Play type name has to be passed without 'is_'.
	 * 'mobile' but not 'is_mobile'
	 */
	public function getByPlayTypes($play_types = array(), $conditions = array()) {
		if (!empty($play_types)) {
			$play_conditions = [
				'is_mobile' => false,
				'is_fix' => false,
				'is_internet' => false,
				'is_tv' => false,
			];
			foreach ($play_types as $play_type) {

				$play_conditions['is_' . $play_type] = true;
			}
			$conditions = array_merge($conditions, $play_conditions);
		}
		return $this->findAll($conditions);
	}
}