<?php

namespace AstelSDK\Exception;

class DataException extends \Exception {
	public function __construct($message, $code = 0, Exception $previous = null) {
		// TODO LOG
		
		parent::__construct($message, $code, $previous);
	}
	
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
	
}