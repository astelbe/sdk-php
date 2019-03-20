<?php

namespace AstelSDK;

use CakeUtility\Hash;

class APIResponse implements \Iterator {
	
	const RESULT_SUCESS = 'success';
	const RESULT_VALIDATION_ERROR = 'validation_error';
	const RESULT_FAILURE = 'failure';
	
	const FIND_TYPE_RAW = 'raw';
	const FIND_TYPE_ALL = 'all';
	const FIND_TYPE_FIRST = 'first';
	const FIND_TYPE_COUNT = 'count';
	
	private $resultData = [];
	private $position = 0;
	private $findType = self::FIND_TYPE_RAW;
	private $resultHeaders = [];
	/**
	 * @var string Gives the level of success of the API response. Default Failure.
	 */
	private $resultSuccessLevel = self::RESULT_FAILURE;
	
	public function rewind() {
		$this->position = 0;
	}
	
	public function current() {
		return $this->resultData[$this->position];
	}
	
	public function key() {
		return $this->position;
	}
	
	public function next() {
		++$this->position;
	}
	
	public function valid() {
		return isset($this->resultData[$this->position]) && !empty($this->resultData[$this->position]);
	}
	
	public function setPosition($position) {
		$this->position = $position;
	}
	
	public function setCurrent($data) {
		$this->resultData[$this->position] = $data;
	}
	
	public function setResultSuccessLevel($level = self::RESULT_SUCESS) {
		$this->resultSuccessLevel = $level;
	}
	
	public function getResultSucessLevel() {
		return $this->resultSuccessLevel;
	}
	
	public function isResultSucess() {
		return $this->resultSuccessLevel === self::RESULT_SUCESS;
	}
	
	public function isResultFailure() {
		return $this->resultSuccessLevel === self::RESULT_FAILURE;
	}
	
	public function isResultValidationError() {
		return $this->resultSuccessLevel === self::RESULT_VALIDATION_ERROR;
	}
	
	public function setHeader($rawHeaders) {
		$headers = [];
		foreach (explode("\r\n", $rawHeaders) as $i => $line) {
			if ($i === 0) {
				$headers['http_code'] = $line;
			} else {
				list ($key, $value) = explode(': ', $line);
				if ($key !== '') {
					$headers[$key] = $value;
				}
			}
		}
		
		$this->resultHeaders = Hash::merge($this->resultHeaders, $headers);
	}
	
	public function getHeader() {
		return $this->resultHeaders;
	}
	
	public function setResultDataArray($data) {
		if ($data !== null && !empty($data)) {
			if (isset($data[0])) {
				// it means there is more than 1 element in the result : find('all'
				$this->findType = self::FIND_TYPE_ALL;
				$this->resultData = Hash::merge($this->resultData, $data);
			} else {
				// it means there is only 1 element in the result : find('first'
				$this->findType = self::FIND_TYPE_FIRST;
				$this->resultData[] = $data;
			}
		}
	}
	
	public function setResultDataJson($data) {
		if ($data === null || $data === '') {
			$returnArray = [];
		} else {
			$data = str_replace('</br>', '<br>', $data);
			$returnArray = @json_decode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			if ($returnArray === null) {
				throw new DataException('An error occurred when decoding the remote data : JSON error: ' .
					json_last_error_msg(), 500);
			}
		}
		$this->setResultDataArray($returnArray);
	}
	
	public function setResultDataRaw($data) {
		$this->findType = self::FIND_TYPE_RAW;
		$this->resultData[] = $data;
	}
	
	public function getResultData() {
		return $this->resultData;
	}
	
	/**
	 * @return array|mixed
	 */
	public function getResultDataAccordingFindType() {
		if ($this->findType === self::FIND_TYPE_FIRST) {
			return $this->resultData[0];
		}
		
		return $this->resultData;
	}
}
