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
	const RETURN_SINGLE_ELEMENT = 1;
	const RETURN_MULTIPLE_ELEMENTS = 0;
	const RETURN_CONTENT = 2;
	
	public function __construct() {
		$this->context = AstelContext::getInstance();
	}
	
	protected function isInCache() {
	
	}
	
	/**
	 * TODO REMOVE
	 *
	 * @param $token
	 */
	public function setToken($token) {
		$this->token = $token;
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
	protected function addUrlParams($url, $params, $is_filter = false) {
		$url_params = [];
		if (!isset($params['conditions'])) {
			$params['conditions'] = [];
		}
		
		// Add params
		$url_params = $this->arrayToURLGETParams($params['conditions'], 'filters', $is_filter);
		if (isset($params['contain'])) {
			$url_params = array_merge($url_params, $this->arrayToURLGETParams($params['contain'], 'contain', $is_filter));
		}
		
		$url_params = implode('&', $url_params);
		if (!empty($url_params)) {
			$url .= '?' . $url_params;
		}
		
		return $url;
	}
	
	protected function arrayToURLGETParams($params, $varName, $useVarName = false) {
		$url_params = [];
		foreach ($params as $k => $param) {
			if (is_array($param)) {
				foreach ($param as $tempId => $sub) {
					$url_params[] = $varName . '[' . $k . '][' . $tempId . ']=' . $sub;
				}
			} else {
				if (!$useVarName) {
					$url_params[] = $k . '=' . $param;
				} else {
					$url_params[] = $varName . '[' . $k . ']=' . $param;
				}
			}
		}
		
		return $url_params;
	}
	
	protected function setUrl($url) {
		curl_setopt(
			$this->ch,
			CURLOPT_URL,
			'https://' . $this->apiParticle . $this->context->getEnv() . '.astel.be/' . $url
		);
	}
	
	protected function setApiParticle($particle) {
		$this->apiParticle = $particle;
	}
	
	protected function setPost(array $data = []) {
		curl_setopt($this->ch, CURLOPT_POST, true);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($data));
	}
	
	protected function init() {
		$this->ch = curl_init();
		$headers = [
			'Cache-Control: no-cache',
			'Token: ' . $this->context->getPartnerToken(),
			'x-api-key: ' . $this->context->getPartnerToken(),
		];
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
	}
	
	protected function exec($return_type = self::RETURN_MULTIPLE_ELEMENTS) {
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
		$call_server_status = Hash::get($returnArray, '0.status', 0);
		if ($call_server_status !== 1) {
			$errorMsg = Hash::get($returnArray, '0.message', 'Unknown Error');
			throw new DataException('An error occurred when retrieving the remote data : API error: ' . $errorMsg, 500);
		}
		
		$this->lastCallStatus = $returnArray[0];
		
		if ($return_type === self::RETURN_SINGLE_ELEMENT) {
			// Return only element (array[1][0])
			return Hash::get($this->cleanContentFromBr($returnArray), '1.0', []);
		} elseif ($return_type === self::RETURN_MULTIPLE_ELEMENTS) {
			// Return only elements (array[1])
			return Hash::get($this->cleanContentFromBr($returnArray), '1', []);
		}
		
		return false;
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