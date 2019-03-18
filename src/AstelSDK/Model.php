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
	protected $cacheResults = []; // To use only for single product
	protected $apiParticle = 'api';
	protected $lastQuery = null;
	
	public function __construct() {
		$this->context = AstelContext::getInstance();
	}
	
	protected function isInCache() {
	
	}
	
	public function setApiParticle($particle) {
		$this->apiParticle = $particle;
	}
	
	/**
	 * @return APIQuery object
	 */
	public function newQuery() {
		$this->lastQuery = new APIQuery($this->apiParticle);
		
		return $this->lastQuery;
	}
	
	public function getNextElements() {
		// TODO pagination, get next, $this->lastQuery (get the last query type, create a new one and query the next elements)
	}
	
	public function getPreviousElements() {
		// TODO pagination, get next, $this->lastQuery (get the last query type, create a new one and query the previous elements)
	}
	
	public function getLastElements() {
		// TODO pagination
	}
	
	public function getCountElements() {
		// TODO pagination
		
		// TODO Create a result object  with these options ?
	}
	
	public function exists($id) {
		$is_exit = $this->find('first', ['conditions' => ['id' => $id]]);
		
		return $is_exit !== false && !empty($is_exit);
	}
	
	public function find($type, array $params = []) {
		$cacheKey = md5($type . print_r($params, true));
		if (isset($this->cacheResults[$cacheKey])) {
			return $this->cacheResults[$cacheKey];
		}
		$returnArray = [];
		if ($type === 'first') {
			$first = $this->getFirst($params);
			$returnArray = $this->extractResultEmbedded($first);
		} elseif ($type === 'all') {
			$all = $this->getAll($params);
			if (!empty($all)) {
				foreach ($all as $key => $returnElt) {
					$returnArray[$key] = $this->extractResultEmbedded($returnElt);
				}
			}
		}
		$this->cacheResults[$cacheKey] = $returnArray;
		
		$returnArray = $this->cleanContentFromBr($returnArray);
		
		return $returnArray;
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
	
	/**
	 * Replace </br> by <br> to respect W3C convention
	 * called on exec before returning data
	 *
	 * @param $content
	 *
	 * @return array|mixed
	 */
	public function cleanContentFromBr($content) {
		if (is_array($content)) {
			$out = [];
			foreach ($content as $k => $v) {
				$out[$k] = $this->cleanContentFromBr($v);
			}
		} else {
			$out = str_replace('</br>', '<br>', $content);
		}
		
		return $out;
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