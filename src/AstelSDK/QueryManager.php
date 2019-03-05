<?php

namespace AstelSDK;

use CakeUtility\Hash;

/**
 * Class QueryManager
 *
 * @package AstelSDK
 */
abstract class QueryManager extends Singleton {
	
	private $ch;
	private $token;
	protected $context;
	protected $cacheResults = []; // To use only for single product
	protected $lastCallStatus = [];
	protected $apiParticle = 'api';
	private $lastUrl = '';
	private $lastPostData = [];
	protected $lastReturnedData;
	const RETURN_SINGLE_ELEMENT = 1;
	const RETURN_MULTIPLE_ELEMENTS = 0;
	const RETURN_CONTENT = 2;
	
	public function __construct() {
		$this->context = AstelContext::getInstance();
	}
	
	protected function isInCache() {
	
	}
	
	public function getUserIP() {
		$ip = $_SERVER['REMOTE_ADDR'];
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] !== '') {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		
		return $ip;
	}
	
	/**
	 * TODO REMOVE
	 *
	 * @param $token
	 */
	public function setToken($token) {
		$this->token = $token;
	}
	
	public function getUniqueVisitorKey() {
		return md5($this->getUserIP() . $_SERVER['HTTP_USER_AGENT']);
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
		if ($type === 'first') {
			$first = $this->getFirst($params);
			$this->cacheResults[$cacheKey] = $first;
			
			return $first;
		} elseif ($type === 'all') {
			$all = $this->getAll($params);
			$this->cacheResults[$cacheKey] = $all;
			
			return $all;
		}
		
		return false;
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
	
	public function getLastCallStatus() {
		return $this->lastCallStatus;
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
	protected function addUrlParams($url, array $params) {
		// Add param
		$url_params = $this->arrayToURLGETParams($params);
		
		$url_params = implode('&', $url_params);
		if (!empty($url_params)) {
			$url .= '?' . $url_params;
		}
		
		return $url;
	}
	
	protected function arrayToURLGETParams(array $params) {
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
	
	protected function setUrl($url) {
		$this->lastUrl = $url;
		curl_setopt(
			$this->ch,
			CURLOPT_URL,
			'https://' . $this->apiParticle . $this->context->getEnv() . '.astel.be/' . $url
		);
	}
	
	public function setApiParticle($particle) {
		$this->apiParticle = $particle;
	}
	
	protected function setPost(array $data = []) {
		$this->lastPostData = $data;
		curl_setopt($this->ch, CURLOPT_POST, true);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($data));
	}
	
	protected function setDelete() {
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	}
	
	protected function init() {
		$this->ch = curl_init();
		$headers = [
			'Cache-Control: no-cache',
			'x-api-key: ' . $this->context->getPartnerToken(),
		];
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
	}
	
	protected function exec($return_type = self::RETURN_MULTIPLE_ELEMENTS) {
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
	
	protected function log($message, $level = 'notice', $context = []) {
		return $this->context->log($message, $level, $context);
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
	
	/**
	 * @param $return_type
	 *
	 * @return bool|mixed
	 * @throws DataException
	 */
	protected function exec_process($return_type) {
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
			if ($http_status !== 200) {
				throw new DataException('An error occurred when accessing internally the remote data. Error HTTP: ' .
					$http_status, 500);
			} else {
				if ($output === '') {
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
			if ($return_type === self::RETURN_SINGLE_ELEMENT) {
				$returnArray = $this->extractResultEmbedded($returnArray);
			} elseif ($return_type === self::RETURN_MULTIPLE_ELEMENTS) {
				foreach ($returnArray as $key => $returnElt) {
					$returnArray[$key] = $this->extractResultEmbedded($returnElt);
				}
			}
		}
		
		/*
		 * TODO handled now with http codes
		 $call_server_status = Hash::get($returnArray, '0.status', 0);
		if ($call_server_status !== 1) {
			$errorMsg = Hash::get($returnArray, '0.message', 'Unknown Error');
			throw new DataException('An error occurred when retrieving the remote data : API error: ' . $errorMsg, 500);
		}*/
		
		//$this->lastCallStatus = $returnArray[0];
		
		return $this->cleanContentFromBr($returnArray);
		
	}
	
	/**
	 * @param array $resultArray
	 *
	 * @return array
	 */
	protected function extractResultEmbedded($resultArray) {
		if (isset($resultArray['_embedded']) && !empty($resultArray['_embedded'])) {
			foreach ($resultArray['_embedded'] as $embeddedModelName => $embeddedValue) {
				$resultArray[$embeddedModelName] = $this->extractResultEmbedded($embeddedValue);
			}
			unset($resultArray['_embedded']);
		}
		
		return $resultArray;
	}
	
}