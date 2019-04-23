<?php

namespace AstelSDK\Model;

class Validation {
	
	protected $errors = [];
	protected $warnings = [];
	protected $data = [];
	protected $parameters = [];
	
	public function __construct(array $errors, array $warnings, array $data, array $parameters) {
		$this->errors = $errors;
		$this->warnings = $warnings;
		$this->data = $data;
		$this->parameters = $parameters;
	}
	
	public function hasErrors() {
		return !empty($this->errors);
	}
	
	public function hasWarnings() {
		return !empty($this->warnings);
	}
	
	public function getErrors() {
		return $this->errors;
	}
	
	public function getWarnings() {
		return $this->warnings;
	}
	
	public function errorsToStringBR() {
		$out = '';
		foreach ($this->errors as $field => $errorMsg) {
			$out .= $errorMsg . '<br />';
		}
		
		return $out;
	}
}