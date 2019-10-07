<?php

namespace AstelSDK\API;

use CakeUtility\Hash;
use AstelSDK\AstelContext;
use AstelSDK\Exception\DataException;

class APIQuery {
	protected $lastCallStatus = [];
	protected $apiParticle = 'api';
	protected $lastUrl = '';
	protected $lastPostData = [];
	protected $lastReturnedData;
	protected $ch;
	protected $url = '';
	protected $urlParams = '';
	protected $postParams = [];
	protected $headers = [];
	protected $Cacher = null;
	protected $cacheTTL = null;
	
	protected $method = self::HTTP_GET;
	
	const RETURN_CONTENT = 2;
	const HTTP_GET = 'GET';
	const HTTP_POST = 'POST';
	const HTTP_DELETE = 'DELETE';
	
	public function __construct($apiParticle, $Cacher, $cacheTTL) {
		$this->apiParticle = $apiParticle;
		$this->context = AstelContext::getInstance();
		
		$this->Cacher = $Cacher;
		if ($this->Cacher !== null && is_object($this->Cacher)) {
			$this->cacheActive = true;
		}
		$this->cacheTTL = $cacheTTL;
	}
	
	public function isCacheActive() {
		// We write DATA, no cache for that
		if ($this->method === self::HTTP_DELETE || $this->method === self::HTTP_POST) {
			return false;
		}
		
		return $this->cacheActive;
	}
	
	public function getCacher() {
		return $this->Cacher;
	}
	
	public function getLastCallStatus() {
		return $this->lastCallStatus;
	}
	
	public function modelName() {
		return strtolower(get_class($this));
	}
	
	public function arrayToURLGETParams(array $params) {
		$url_params = [];
		foreach ($params as $k => $param) {
			if (is_array($param)) {
				foreach ($param as $tempId => $sub) {
					if ($sub === true) {
						$sub = 'true';
					} elseif ($sub === false) {
						$sub = 'false';
					}
					$url_params[] = $k . '[' . $tempId . ']=' . $sub;
				}
			} else {
				if ($param === true) {
					$param = 'true';
				} elseif ($param === false) {
					$param = 'false';
				}
				$url_params[] = $k . '=' . $param;
				
			}
		}
		
		return $url_params;
	}
	
	/**
	 * @param      $url
	 * @param      $params
	 * @param bool $is_filter
	 *
	 * Can compile params or param filters
	 * if is_filter = true, transform array(brand_id => 3) into filters[brand_id]=3, as specified in API V2
	 *     documentation
	 *
	 * @return $url string
	 */
	public function addGETParams(array $params) {
		// Add param
		$url_params = $this->arrayToURLGETParams($params);
		
		$url_params = implode('&', $url_params);
		if (!empty($url_params)) {
			$this->urlParams .= '?' . $url_params;
		}
		
		return $this->urlParams;
	}
	
	public function addPOSTParams(array $params) {
		$this->postParams = array_merge($this->postParams, $params);
		$this->setHTTPMethod(self::HTTP_POST);
	}
	
	public function setHTTPMethod($method = self::HTTP_GET) {
		$this->method = $method;
	}
	
	public function setUrl($url) {
		$this->url = $url;
	}
	
	public function setCurlPost() {
		$this->lastPostData = $this->postParams;
		curl_setopt($this->ch, CURLOPT_POST, true);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($this->postParams));
	}
	
	protected function setCurlDelete() {
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	}
	
	public function addHeader(array $headers) {
		$this->headers = Hash::merge($this->headers, $headers);
		
		return $this->headers;
	}
	
	protected function init() {
		$this->ch = curl_init();
		//curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
		curl_setopt($this->ch, CURLOPT_HEADER, 1);
		curl_setopt($this->ch, CURLOPT_URL, $this->lastUrl);
	}
	
	protected function preInitHeaders() {
		$defaultHeaders = [
			'Cache-Control: no-cache',
			'x-api-key: ' . $this->context->getPartnerToken(),
		];
		
		return $this->addHeader($defaultHeaders);
	}
	
	protected function preInitSetUrl() {
		$this->lastUrl = 'https://' . $this->apiParticle . $this->context->getEnv() . '.astel.be/' . $this->url . $this->urlParams;
	}
	
	/**
	 * @param null $return_type
	 *
	 * @return APIResponse|bool|mixed
	 * @throws \AstelSDK\Exception\ValidationErrorException
	 * @throws \Exception
	 */
	public function exec($return_type = null) {
		$this->preInitHeaders();
		$this->preInitSetUrl();
		if ($this->isCacheActive()) {
			$paramsForCacheKey = ['lastURL' => $this->lastUrl, 'postParams' => $this->postParams, 'method' => $this->method, 'headers' => $this->headers, 'return_type' => $return_type];
			$cacheKey = $this->getCacher()->uKey($this->modelName() . '_exec', $paramsForCacheKey);
			$result = $this->getCacher()->get($cacheKey);
			if (null !== $result && !empty($result)) {
				$responseObject = new APIResponse();
				$responseObject->jsonUnSerialize($result); // retrieve the APIResponse object previously serialized into json
				
				$this->lastReturnedData = $responseObject->getResultData(); // TODO handle data raw
				
				return $responseObject;
			}
		}
		try {
			$result = new APIResponse();
			$this->init();
			
			if ($this->method === self::HTTP_GET) {
				// we already set params with setCurlUrl()
			} elseif ($this->method === self::HTTP_POST) {
				$this->setCurlPost();
			} elseif ($this->method === self::HTTP_DELETE) {
				$this->setCurlDelete();
			}
			$result = $this->exec_process($return_type, $result);
			if ($this->isCacheActive()) {
				if ($result->getHttpCode() === 200 || $result->getHttpCode() === 204) {
					// We don't cache 4xx and 5xx errors !
					// uncomment this for having a trace every cache write
					// debug('Caching - ' . $this->cacheTTL . 's - ' . $cacheKey);
					$this->getCacher()->add($cacheKey, $result, $this->cacheTTL);
				}
			}
		} catch (\Exception $e) {
			$context = [
				'lastURL' => $this->lastUrl,
				'method' => $this->method,
				'postParams' => $this->lastPostData,
				'headers' => $this->headers,
				'message' => $e->getMessage(),
			];
			if ($this->lastReturnedData !== null) {
				$context['returned_content'] = $this->lastReturnedData;
			}
			$this->context->log($e->getMessage(), 'fatal', $context); // Silent logging
			
			if ($this->context->isDebug()) {
				throw $e; // Hard errors
			}
		}
		
		return $result;
	}
	
	/**
	 * @param $return_type
	 *
	 * @return bool|mixed
	 * @throws DataException
	 */
	public function exec_process($return_type, ApiResponse $result) {
		$output = curl_exec($this->ch);
		
		$header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
		$header = substr($output, 0, $header_size);
		$result->setHeader($header);
		$body = substr($output, $header_size);
		
		$http_status = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
		$result->setHttpCode($http_status);
		$curl_errno = curl_errno($this->ch);
		$curl_error = curl_error($this->ch);
		curl_close($this->ch);
		
		$returnArray = null;
		if ($output === false || $curl_errno > 0) {
			throw new DataException('An error occurred when accessing internally the data. Error Curl (' .
				$curl_errno . '): ' . $curl_error, 500);
		} else {
			if ($http_status !== 200 && $http_status !== 204 && $http_status !== 400) {
				$result->setResultSuccessLevel(ApiResponse::RESULT_FAILURE);
				$result->setResultDataRaw($body);
				
				return $result;
			} else {
				if ($http_status === 204) {
					$result->setResultSuccessLevel(ApiResponse::RESULT_SUCESS);
					$result->setResultDataArray([]);
					
					return $result;
				} else {
					$this->lastReturnedData = $body;
					
					if ($http_status === 400) {
						$result->setResultSuccessLevel(ApiResponse::RESULT_VALIDATION_ERROR);
					} else {
						$result->setResultSuccessLevel(ApiResponse::RESULT_SUCESS);
					}
					if ($return_type === self::RETURN_CONTENT) {
						// Return directly the content
						$result->setResultDataRaw($body);
						
						return $result;
					} else {
						$result->setResultDataJson($body);
					}
					
				}
			}
		}
		$this->lastReturnedData = $result->getResultData();
		
		return $result;
	}
	
}
