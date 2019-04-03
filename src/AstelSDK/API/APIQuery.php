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
	
	protected $method = self::HTTP_GET;
	
	const RETURN_CONTENT = 2;
	const HTTP_GET = 'GET';
	const HTTP_POST = 'POST';
	const HTTP_DELETE = 'DELETE';
	
	public function __construct($apiParticle) {
		$this->apiParticle = $apiParticle;
		$this->context = AstelContext::getInstance();
	}
	
	public function getLastCallStatus() {
		return $this->lastCallStatus;
	}
	
	public function arrayToURLGETParams(array $params) {
		$url_params = [];
		foreach ($params as $k => $param) {
			if (is_array($param)) {
				foreach ($param as $tempId => $sub) {
					$url_params[] = $k . '[' . $tempId . ']=' . $sub;
				}
			} else {
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
	
	protected function setCurlUrl() {
		$this->lastUrl = 'https://' . $this->apiParticle . $this->context->getEnv() . '.astel.be/' . $this->url . $this->urlParams;
		curl_setopt($this->ch, CURLOPT_URL, $this->lastUrl);
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
		$defaultHeaders = [
			'Cache-Control: no-cache',
			'x-api-key: ' . $this->context->getPartnerToken(),
		];
		$headers = $this->addHeader($defaultHeaders);
		//curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
		
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
		curl_setopt($this->ch, CURLOPT_HEADER, 1);
	}
	
	/**
	 * @param null $return_type
	 *
	 * @return APIResponse|bool|mixed
	 * @throws \Exception
	 */
	public function exec($return_type = null) {
		$result = new APIResponse();
		$this->init();
		$this->setCurlUrl();
		
		if ($this->method === self::HTTP_GET) {
			// we already set params with setCurlUrl()
		} elseif ($this->method === self::HTTP_POST) {
			$this->setCurlPost();
		} elseif ($this->method === self::HTTP_DELETE) {
			$this->setCurlDelete();
		}
		
		try {
			$result = $this->exec_process($return_type, $result);
		} catch (\Exception $e) {
			$context = [
				'token' => $this->context->getPartnerToken(),
				'APIParticle' => $this->apiParticle,
				'APIEnv' => $this->context->getEnv(),
				'lastURL' => $this->lastUrl,
				'lastPostData' => $this->lastPostData,
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
		$curl_errno = curl_errno($this->ch);
		$curl_error = curl_error($this->ch);
		curl_close($this->ch);
		
		$returnArray = null;
		if ($output === false || $curl_errno > 0) {
			throw new DataException('An error occurred when accessing internally the data. Error Curl (' .
				$curl_errno . '): ' . $curl_error, 500);
		} else {
			if ($http_status !== 200 && $http_status !== 204) {
				// TODO Handles 4xx errors and validation errors
				throw new DataException('An error occurred when accessing internally the remote data. Error HTTP: ' .
					$http_status, 500);
			} else {
				if ($http_status === 204) {
					$result->setResultSuccessLevel(ApiResponse::RESULT_SUCESS);
					$result->setResultDataArray([]);
					
					return $result;
				} elseif ($body === '') {
					throw new DataException('An error occurred when decoding the remote data. No return from API datasource.', 500);
				} else {
					$this->lastReturnedData = $body;
					if ($return_type === self::RETURN_CONTENT) {
						// Return directly the content
						$result->setResultSuccessLevel(ApiResponse::RESULT_SUCESS);
						$result->setResultDataRaw($body);
						
						return $result;
					}
					$result->setResultSuccessLevel(ApiResponse::RESULT_SUCESS);
					$result->setResultDataJson($body);
				}
			}
		}
		$this->lastReturnedData = $result->getResultData();
		
		return $result;
	}
	
}
