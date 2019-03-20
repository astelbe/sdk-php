<?php

namespace AstelSDK;

use CakeUtility\Hash;

/**
 * Class QueryManager
 *
 * @package AstelSDK
 */
abstract class Model extends Singleton {
	
	protected $context;
	protected $cacheResults = [];
	protected $apiParticle = 'api';
	protected $lastQueryObject = null;
	protected $lastFindParams = [];
	protected $lastResponseObject = null;
	
	public function __construct() {
		$this->context = AstelContext::getInstance();
		$this->setApiParticle($this->context->getApiParticle());
	}
	
	public function setApiParticle($particle) {
		$this->apiParticle = $particle;
	}
	
	/**
	 * @return APIQuery object
	 */
	protected function newQuery() {
		$this->lastQueryObject = new APIQuery($this->apiParticle);
		
		return $this->lastQueryObject;
	}
	
	public function exists($id) {
		$is_exit = $this->find('first', ['conditions' => ['id' => $id]]);
		
		return $is_exit !== false && !empty($is_exit);
	}
	
	public function find($type, array $params = [], $getFullResponseObject = false) {
		$this->lastFindParams = ['type' => $type, 'params' => $params, 'getFullResponseObject' => $getFullResponseObject];
		$cacheKey = md5($type . print_r($params, true));
		if (isset($this->cacheResults[$cacheKey])) {
			return $this->cacheResults[$cacheKey];
		}
		$response = null;
		if ($type === 'first') {
			$response = $this->getFirst($params);
		} elseif ($type === 'all') {
			$response = $this->getAll($params);
		}
		$this->lastResponseObject = clone $response;
		
		if ($response->valid()) {
			foreach ($response as $key => $returnElt) {
				$returnArray = $this->extractResultEmbedded($returnElt);
				$response->setCurrent($returnArray);
			}
		}
		
		$this->cacheResults[$cacheKey] = $response;
		
		if (!$getFullResponseObject) {
			// return the arrayAll/arrayFind/count/raw version of the response
			return $response->getResultDataAccordingFindType();
		}
		
		return $response;
	}
	
	protected function findPaginate($paginationDirection) {
		$lastFindType = Hash::get($this->lastFindParams, 'type');
		$this->lastResponseObject->rewind();
		if ($lastFindType === ApiResponse::FIND_TYPE_ALL && null !== $this->lastResponseObject && $this->lastResponseObject->valid()) {
			$collectionMetadata = $this->lastResponseObject->getCollectionMetadata();
			if ($paginationDirection === 'count') {
				return Hash::get($collectionMetadata, 'total_items');
			}
			$nextLink = Hash::get($collectionMetadata, '_links.' . $paginationDirection . '.href');
			if (null !== $nextLink) {
				$paramsNextElements = $this->urlToGetParamsArray($nextLink);
				if ($paramsNextElements !== false) {
					return $this->find('all', $paramsNextElements);
				}
			}
		}
		
		return false;
	}
	
	private function urlToGetParamsArray($url) {
		if ($url === '') {
			return false;
		}
		$params = [];
		$explodedGetParams = explode('?', $url, 2);
		if (isset($explodedGetParams[1])) {
			$params = $this->urlToGetParamsArrayHandlesParams($explodedGetParams[1]);
		}
		
		return $params;
	}
	
	private function urlToGetParamsArrayHandlesParams($urlGetParams) {
		$a = [];
		foreach (explode('&', $urlGetParams) as $q) {
			$p = explode('=', $q, 2);
			$a[$p[0]] = isset ($p[1]) ? $p[1] : '';
		}
		
		return $a;
	}
	
	public function findNextElements() {
		return $this->findPaginate('next');
	}
	
	public function findPreviousElements() {
		return $this->findPaginate('previous');
	}
	
	public function findLastElements() {
		return $this->findPaginate('last');
	}
	
	public function findCountElements() {
		return $this->findPaginate('count');
	}
	
	/**
	 * @param array $resultArray
	 *
	 * @return array
	 */
	protected function extractResultEmbedded($resultArray) {
		if (isset($resultArray['_embedded']['items'])) {
			// For Collection
			if (empty($resultArray['_embedded']['items'])) {
				$resultArray = [];
			} else {
				$res = [];
				foreach ($resultArray['_embedded']['items'] as $tmpID => $result) {
					$res[$tmpID] = $this->extractResultEmbedded($result);
				}
				$resultArray = $res;
			}
		} else {
			// For Item
			if (isset($resultArray['_embedded']) && !empty($resultArray['_embedded'])) {
				foreach ($resultArray['_embedded'] as $embeddedModelName => $embeddedValue) {
					$resultArray[$embeddedModelName] = $this->extractResultEmbedded($embeddedValue);
				}
				unset($resultArray['_embedded']);
			}
			unset($resultArray['_links']);
		}
		
		return $resultArray;
	}
	
	public function create(array $data = []) {
		return $this->createFirst($data);
	}
	
	public function transformIdToReturnedArray(array $array = [], $idName) {
		$out = [];
		foreach ($array as $a) {
			$out[Hash::get($a, $idName)] = $a;
		}
		
		return $out;
		
	}
	
	protected function log($message, $level = 'notice', $context = []) {
		return $this->context->log($message, $level, $context);
	}
	
}