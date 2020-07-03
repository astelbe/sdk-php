<?php

namespace AstelSDK\Utils;

use CakeUtility\File;

class Logger {
	
	protected $logFile = null;
	protected $context = null;
	protected $is_writable = false;
	
	public function __construct($path = '', $context) {
		if ($path !== '') {
			$this->logFile = new File($path . '/astel_sdk.log', true);
			$this->is_writable = true;
		}
		$this->context = $context;
	}
	
	public function append($message, $level, $context = []) {
		if ($this->is_writable) {
			$toEncode = [
				'date' => date('Y-m-d H:m:s', time()),
				'level' => $level,
				'message' => $message,
			];
			if (!empty($context)) {
				$toEncode['context'] = $context;
			}
			
			return $this->logFile->append(json_encode($toEncode) . "\r\n");
		}
		
		return false;
	}
}