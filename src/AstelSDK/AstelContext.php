<?php

namespace AstelSDK;

class AstelContext extends Singleton {
	
	protected $version = '2018121201';
	
	protected $env;
	protected $partnerToken;
	protected $isPrivate = null;
	protected $language = 'FR';
	
	public function __construct($env = 'sta', $partnerToken = '') {
		if ($env === 'prod') {
			$env = '';
		}
		$this->setEnv($env);
		$this->setPartnerToken($partnerToken);
		parent::__construct();
		self::$instances['AstelSDK\AstelContext'] = $this; // for singleton future use
	}
	
	/**
	 * @return string
	 */
	public function getEnv() {
		return $this->env;
	}
	
	/**
	 * @param string $env
	 */
	public function setEnv($env) {
		$this->env = $env;
	}
	
	/**
	 * @return string
	 */
	public function getPartnerToken() {
		return $this->partnerToken;
	}
	
	/**
	 * @param string $partnerToken
	 */
	public function setPartnerToken($partnerToken) {
		$this->partnerToken = $partnerToken;
	}
	
	/**
	 * @return mixed
	 */
	public function getisPrivate() {
		return $this->isPrivate;
	}
	
	/**
	 * Determines if all API calls should be filtered by default by private / professional.
	 * If not set, (set to null), no conditions are added to API calls
	 *
	 * @param mixed $isPrivate
	 */
	public function setIsPrivate($isPrivate) {
		$this->isPrivate = $isPrivate;
	}
	
	/**
	 * @return string
	 */
	public function getLanguage() {
		return $this->language;
	}
	
	/**
	 * @param string $language
	 */
	public function setLanguage($language) {
		$this->language = $language;
	}
	
	/**
	 * @return string
	 */
	public function getVersion() {
		return $this->version;
	}
	
	/**
	 * @param string $version
	 */
	public function setVersion($version) {
		$this->version = $version;
	}
}