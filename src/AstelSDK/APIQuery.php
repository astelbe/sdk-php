<?php

namespace AstelSDK;

use CakeUtility\Hash;

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
		curl_setopt(
			$this->ch,
			CURLOPT_URL,
			$this->lastUrl
		);
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
		
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
	}
	
	public function exec($return_type = null) {
		$this->init();
		$this->setCurlUrl();
		
		if ($this->method === self::HTTP_GET) {
			// we already set params with setCurlUrl()
		} elseif ($this->method === self::HTTP_POST) {
			$this->setCurlPost();
		} elseif ($this->method === self::HTTP_DELETE) {
			$this->setCurlDelete();
		}
		
		$result = [];
		try {
			$result = $this->exec_process($return_type);
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
			$this->log($e->getMessage(), 'fatal', $context); // Silent logging
			
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
	public function exec_process($return_type) {
		$output = curl_exec($this->ch);
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
				throw new DataException('An error occurred when accessing internally the remote data. Error HTTP: ' .
					$http_status, 500);
			} else {
				if ($http_status === 204) {
					return [];
				} elseif ($output === '') {
					throw new DataException('An error occurred when decoding the remote data. No return from API datasource.', 500);
				} else {
					$this->lastReturnedData = $output;
					if ($return_type === self::RETURN_CONTENT) {
						// Return directly the content
						return $output;
					}
					$returnArray = @json_decode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
					if ($returnArray === null) {
						throw new DataException('An error occurred when decoding the remote data : JSON error: ' .
							json_last_error_msg(), 500);
					}
				}
			}
		}
		$this->lastReturnedData = $returnArray;
		
		if (!empty($returnArray)) {
			// returns multiple elements
			if (isset($returnArray[0])) {
				foreach ($returnArray as $key => $returnElt) {
					$returnArray[$key] = $this->extractResultEmbedded($returnElt);
				}
			} else {
				// Single element
				$returnArray = $this->extractResultEmbedded($returnArray);
			}
		}
		
		return $this->cleanContentFromBr($returnArray);
		
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
}
