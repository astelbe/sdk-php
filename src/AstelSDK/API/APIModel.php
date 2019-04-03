<?php

namespace AstelSDK\API;

use CakeUtility\Hash;
use AstelSDK\AstelContext;
use AstelSDK\Utils\Singleton;
use AstelSDK\Utils\URL;
use AstelSDK\Utils\HALOperations;

/**
 * Class Model :
 * Abstract class of every API Models containing operations you can perform on them.
 *
 * @package AstelSDK
 */
abstract class APIModel extends Singleton {
	
	protected $context;
	protected $cacheResults = [];
	protected $apiParticle = 'api';
	protected $lastQueryObject = null;
	protected $lastFindParams = [];
	protected $lastResponseObject = null;
	
	const FIND_TYPE_ALL = 'all';
	const FIND_TYPE_FIRST = 'first';
	
	/**
	 * Model constructor.
	 *
	 * Get the context and default api particle
	 */
	public function __construct() {
		$this->context = AstelContext::getInstance();
		$this->setApiParticle($this->context->getApiParticle());
	}
	
	/**
	 * Used to force this Model to use a different ApiParticle
	 *
	 * @param $particle particle to use for the calls of this Model
	 */
	public function setApiParticle($particle) {
		$this->apiParticle = $particle;
	}
	
	/**
	 * Checks if an element exists
	 *
	 * @param $id ID of the element to verify if existent
	 *
	 * @return bool True if the element with the given ID exists, False otherwise.
	 */
	public function exists($id) {
		$is_exit = $this->find('first', ['id' => $id]);
		
		return $is_exit !== false && !empty($is_exit);
	}
	
	/**
	 * Allows to find any element of the current Model served by the Astel API.
	 * Allows the CRUD operation READ.
	 *
	 * @param $type string 'first' or 'all' for retrieving a single element or all elements. For 'all' elements,
	 * the result is always paginated, please use this->findNextElements() for next page or use params count / page
	 * @param array $params params of the API call : filters, pagination, embed,...
	 *
	 * @return array Element returned by the API call in a simple structured array (API is using HAL convention, the
	 * returned array abstracts the logic in a friendly manner)
	 *
	 */
	public function find($type = self::FIND_TYPE_ALL, array $params = []) {
		$this->lastFindParams = ['type' => $type, 'params' => $params];
		$cacheKey = md5($type . print_r($params, true));
		if (isset($this->cacheResults[$cacheKey])) {
			return $this->cacheResults[$cacheKey];
		}
		$response = null;
		if ($type === self::FIND_TYPE_FIRST) {
			$response = $this->getFirst($params);
		} elseif ($type === self::FIND_TYPE_ALL) {
			$response = $this->getAll($params);
		}
		$return = $this->returnResponse($response);
		$this->cacheResults[$cacheKey] = $return;
		
		return $return;
	}
	
	/**
	 * Used to process and convert an APIResponse to a easily manipulable array
	 *
	 * @param APIResponse $response response of an API call
	 *
	 * @return array easily manipulable array with HAL logic interpreted
	 */
	protected function returnResponse($response) {
		
		if (is_bool($response)) {
			$this->lastResponseObject = new APIResponse();
			$this->lastResponseObject->setResultSuccessLevel(APIResponse::RESULT_FAILURE);
			
			return $response;
		}
		$this->lastResponseObject = clone $response;
		
		if ($response->valid()) {
			foreach ($response as $key => $returnElt) {
				$returnArray = HALOperations::interpretHALLogicToSimpleArray($returnElt);
				$response->setCurrent($returnArray);
			}
		}
		
		// return the arrayAll/arrayFind/count/raw version of the response
		return $response->getResultDataAccordingFindType();
	}
	
	/**
	 * Get the Full API response Object
	 *
	 * @return object|null After a find result is retrieved, you can call this function to retrieve the full response
	 * object containing headers, and HAL info of API response.
	 */
	public function findLastFullResponseObject() {
		return $this->lastResponseObject;
	}
	
	/**
	 * Used by pagination, get the next page of items after a find('all') that gets the current elements
	 *
	 * @return array|bool False if the last find returns no more than 1 result. Array of the next page of items otherwise.
	 */
	public function findNextElements() {
		return $this->findPaginate('next');
	}
	
	/**
	 * Used by pagination, get the previous page of items after a find('all') that gets the current elements
	 *
	 * @return array|bool False if the last find returns no more than 1 result. Array of the previous page of items otherwise.
	 */
	public function findPreviousElements() {
		return $this->findPaginate('previous');
	}
	
	/**
	 * Used by pagination, get the last page of items after a find('all') that gets the current elements
	 *
	 * @return array|bool False if the last find returns no more than 1 result. Array of the last page of items otherwise.
	 */
	public function findLastElements() {
		return $this->findPaginate('last');
	}
	
	/**
	 * Used after a find('all'), gets the total items that can be retrieved by the paginated call
	 *
	 * @return integer Total number of elements paginated
	 */
	public function findCountElements() {
		return $this->findPaginate('count');
	}
	
	/**
	 * Allows the CRUD operation of Create / Update.
	 *
	 * @param array $data
	 *
	 * @return boolean Success of the object creation
	 */
	public function create(array $data = []) {
		return $this->createFirst($data);
	}
	
	/**
	 * @return APIQuery object New APIQuery Object
	 */
	protected function newQuery() {
		$this->lastQueryObject = new APIQuery($this->apiParticle);
		
		return $this->lastQueryObject;
	}
	
	protected function findPaginate($paginationDirection) {
		$lastFindType = Hash::get($this->lastFindParams, 'type');
		$this->lastResponseObject->rewind();
		if ($lastFindType === self::FIND_TYPE_ALL && null !== $this->lastResponseObject && $this->lastResponseObject->valid()) {
			$collectionMetadata = $this->lastResponseObject->getCollectionMetadata();
			if ($paginationDirection === 'count') {
				return Hash::get($collectionMetadata, 'total_items');
			}
			$nextLink = Hash::get($collectionMetadata, '_links.' . $paginationDirection . '.href');
			if (null !== $nextLink) {
				$paramsNextElements = Url::urlToGetParamsArray($nextLink);
				if ($paramsNextElements !== false) {
					return $this->find('all', $paramsNextElements);
				}
			}
		}
		
		return false;
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