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
	
	public function findNextElements() {
		$lastFindType = Hash::get($this->lastFindParams, 'type');
		$this->lastResponseObject->rewind();
		if ($lastFindType !== ApiResponse::FIND_TYPE_ALL || null === $this->lastResponseObject || !$this->lastResponseObject->valid()) {
			return false;
		}
		//$link = $this->lastResponseObject->getPaginationLinks();
		
		// TODO pagination, get next, $this->lastQuery (get the last query type, create a new one and query the next elements)
	}
	
	public function findPreviousElements() {
		// TODO pagination, get next, $this->lastQuery (get the last query type, create a new one and query the previous elements)
	}
	
	public function findLastElements() {
		// TODO pagination
	}
	
	public function findCountElements() {
		// TODO pagination
		
		// TODO Create a result object  with these options ?
	}
	
	/**
	 * @param array $resultArray
	 *
	 * @return array
	 */
	protected function extractResultEmbedded($resultArray) {
		if (isset($resultArray[0]) && !empty($resultArray[0])) {
			foreach ($resultArray as $tmpID => $result) {
				$resultArray[$tmpID] = $this->extractResultEmbedded($result);
			}
		} else {
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