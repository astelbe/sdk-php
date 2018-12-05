<?php

namespace AstelSDK;

class AstelContext extends Singleton {
	
	protected $env;
	protected $partnerToken;
	protected $isPrivate;
	
	public function __construct($env = 'sta', $partnerToken = '', $isPrivate = true) {
		if ($env === 'prod') {
			$env = '';
		}
		$this->setEnv($env);
		$this->setPartnerToken($partnerToken);
		$this->setIsPrivate($isPrivate);
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
	 * @param mixed $isPrivate
	 */
	public function setIsPrivate($isPrivate) {
		$this->isPrivate = $isPrivate;
	}
}