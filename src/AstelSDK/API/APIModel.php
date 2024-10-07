<?php

namespace AstelSDK\API;

use AstelSDK\Exception\DataException;
use AstelSDK\Exception\ValidationErrorException;
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
  protected $apiParticle = 'api';
  protected $lastQueryObject = null;
  protected $lastFindParams = [];
  protected $lastResponseObject = null;
  protected $Cacher = null;

  protected $customCacheTTL = null;
  protected $disableCache = false;

  const FIND_TYPE_ALL   = 'all';
  const FIND_TYPE_FIRST = 'first';
  const FIND_TYPE_COUNT = 'count';

  /**
   * Model constructor.
   *
   * Get the context and default api particle
   */
  public function __construct() {
    $this->context = AstelContext::getInstance();
    $this->setApiParticle($this->context->getApiParticle());
    $this->Cacher = $this->context->getCacher();
  }

  /**
   * Used to force this Model to use a different ApiParticle
   *
   * @param $particle particle to use for the calls of this Model
   */
  public function setApiParticle($particle) {
    $this->apiParticle = $particle;
  }

  public function modelName() {
    return strtolower(get_class($this));
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
   * @throws ValidationErrorException For 400 errors
   * @throws DataException For 500 errors
   */
  public function find($type = self::FIND_TYPE_ALL, array $params = []) {
    $this->lastFindParams = ['type' => $type, 'params' => $params];

    $response = null;
    if ($type === self::FIND_TYPE_FIRST) {
      $response = $this->getFirst($params);
    } elseif ($type === self::FIND_TYPE_ALL) {
      $response = $this->getAll($params);
    } elseif ($type === self::FIND_TYPE_COUNT) {
      $params['count'] = 1;
      $response = $this->getAll($params);
    }
    $this->handlesResponseThrows($response);
    $return = $this->returnResponse($response, $type);

    return $return;
  }

  /**
   * Finds all items with the following params.
   *
   * @param array $params Array containing all params that will be passed to the API
   *
   * @return array|mixed Return the API V2 Model representation
   */
  public function findAll(array $params = []) {
    if (!isset($params['count'])) {
      $params['count'] = 'max';
    }

    //		TODO cette version findAll ne fontionne pas : les _embed ne sont pas repris dans la pagination
    //		if (!isset($params['page'])) {
    //			$params['page'] = 1;
    //		}
    //		$maxTurns = 50;
    //		$results = $this->find('all', $params);
    //		if (!empty($results)) {
    //			while (true) {
    //				$resultNextPage = $this->findNextElements();
    //				if ($resultNextPage === false || empty($resultNextPage)) {
    //					break;
    //				}
    //				$results = array_merge($results, $resultNextPage);
    //				--$maxTurns;
    //				if ($maxTurns <= 0) {
    //					break;
    //				}
    //			}
    //
    //		}

    // TODO Patch tempoaraire
    $results = [];
    if (!isset($params['page'])) {
      $params['page'] = 0;
    }
    while (true) {
      $params['page']++;
      $resultNextPage = $this->find('all', $params);
      $count = $this->findCountElements();
      if ($count == false) {
        break;
      }
      $results = array_merge($results, $resultNextPage);
    }


    return $results;
  }

  /**
   * Used to process and convert an APIResponse to a easily manipulable array
   *
   * @param APIResponse $response response of an API call
   * @param $type type of result requested 'first','all','count'
   *
   * @return array easily manipulable array with HAL logic interpreted
   */
  protected function returnResponse($response, $type) {
    if (is_object($response)) {
      if ($response->valid()) {
        foreach ($response as $key => $returnElt) {
          $returnArray = HALOperations::interpretHALLogicToSimpleArray($returnElt);
          $response->setCurrent($returnArray);
        }
      }

      // return the arrayAll/arrayFind/count/raw version of the response
      return $response->getResultDataAccordingFindType($type);
    }

    return false;
  }

  /**
   * Get the Full API response Object
   *
   * @return object|null After a find result is retrieved, you can call this function to retrieve the full response
   * object containing headers, and HAL info of API response.
   */
  public function getLastFullResponseObject() {
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

  protected function handlesResponseThrows($response) {
    if (is_bool($response)) {
      $this->lastResponseObject = new APIResponse();
      $this->lastResponseObject->setResultSuccessLevel(APIResponse::RESULT_FAILURE);

      return $response;
    }
    $this->lastResponseObject = clone $response;

    if ($response->isResultFailure()) {
      throw new DataException('An error occurred when accessing internally the remote data. Error HTTP: ' . $this->lastResponseObject->getHttpCode() . ' Data: ' . print_r($this->lastResponseObject->getResultData(), true), 500);
    }
    if ($response->isResultValidationError()) {
      throw new ValidationErrorException('Validations error during the input validation. Please correct input.', 400);
    }
  }

  /**
   *  Allows the CRUD operation of Create / Update.
   *
   * @param array $data
   *
   * @return array Success of the object creation with validation warnings and extra info
   * @throws ValidationErrorException
   * @throws DataException
   */
  public function create(array $data = []) {
    $result = $this->createFirst($data);
    $this->handlesResponseThrows($result);

    return $this->returnResponse($result, 'first'); // In array form
  }

  /**
   * @return APIQuery object New APIQuery Object
   */
  protected function newQuery() {
    if ($this->disableCache) {
      $this->lastQueryObject = new APIQuery($this->apiParticle, $this->Cacher, $ttl);
    } else {
      $ttl = $this->context->getCacheTTL();
      if ($this->customCacheTTL !== null) {
        $ttl = $this->customCacheTTL;
      }
      $this->lastQueryObject = new APIQuery($this->apiParticle, $this->Cacher, $ttl);
    }

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
      if ($paginationDirection === 'next' && $nextLink === null) {
        $nextLink = Hash::get($collectionMetadata, '_links.last.href');
      }
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
