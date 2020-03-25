<?php

namespace AstelSDK;

use AstelSDK\Utils\Singleton;
use AstelSDK\Utils\Logger;

class AstelContext extends Singleton {
	
	protected $version = '2018121201';
	
	protected $env;
	protected $debug;
	public $Logger = null;
	protected $partnerToken;
	protected $isPrivate = null;
	protected $language = 'FR';
	protected $apiParticle = 'api';
	public $Cacher = null;
	protected $cacheTTL = 10800; // 3 hours
	
	public function __construct($env = 'sta', $partnerToken = '', $debug = false, $logPath = '', $cacherObject = null) {
		if ($env === 'prod') {
			$env = '';
		}
		$this->setEnv($env);
		$this->setPartnerToken($partnerToken);
		$this->setDebug($debug);
		parent::__construct();
		self::$instances['AstelSDK\AstelContext'] = $this; // for singleton future use
		$this->Logger = new Logger($logPath, $this);
		$this->Cacher = $cacherObject;
	}
	
	public function setApiParticle($particle) {
		$this->apiParticle = $particle;
	}
	
	public function getApiParticle() {
		return $this->apiParticle;
	}
	
	public function setCacher($object) {
		$this->Cacher = $object;
	}
	
	public function getCacher() {
		return $this->Cacher;
	}
	
	/**
	 * @param $ttl default ttl in s the cache is keeping values
	 */
	public function setCacheTTL($ttl) {
		$this->cacheTTL = $ttl;
	}
	
	public function getCacheTTL(){
		return $this->cacheTTL;
	}
	
	public function log($message, $level = 'notice', $context = []) {
		return $this->Logger->append($message, $level, $context);
	}
	
	/**
	 * @return mixed
	 */
	public function isDebug() {
		return $this->debug;
	}
	
	/**
	 * @param mixed $debug
	 */
	public function setDebug($debug) {
		$this->debug = $debug;
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
		if (!defined('ENV')) {
			define('ENV', $env);
		}
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
	 * @return int (bool)
	 */
	public function getIsProfessional() {
		$is_professional = ($this->getisPrivate() === 1 || $this->getisPrivate() === true || $this->getisPrivate() === null) ? 0 : 1;
		return $is_professional;
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
	 * @return string Used for appeding a string to every CSS / JS for web intergration in order to manage release /
	 * customer navigator refresh of the cached ressource
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
	
	/**
	 * @return string Direct user IP or forwarded IP if the SDK is behind a load balancer
	 */
	public function getUserIP() {
		$ip = $_SERVER['REMOTE_ADDR'];
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] !== '') {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		
		return $ip;
	}
	
	/**
	 * @return string
	 */
	public function getUniqueVisitorKey() {
		return md5($this->getUserIP() . $_SERVER['HTTP_USER_AGENT']);
	}
	
	public function getReferrer() {
		return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
	
	/**
	 * Register functions:
	 * - debug() for pretty display of debug information
	 * - stacktrace() for easy stacktrace print
	 * - h() for htmlspecialchars
	 */
	public static function registerUtilsFunctions() {
		include_once __DIR__ . '/../CakeUtility/basics.php';
	}
}