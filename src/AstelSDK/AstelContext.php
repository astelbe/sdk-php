<?php

namespace AstelSDK;

use AstelSDK\Utils\Singleton;
use AstelSDK\Utils\Logger;
use AstelSDK\Utils\TypeTransform;
use CakeUtility\Hash;

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
	protected $session = null;
	
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
	
	public function initSession() {
		$this->session = new EmulatedSession($this);
	}
	
	public function getSessionID() {
		if ($this->session !== null) {
			return $this->session->getSessionID();
		}
		
		return null;
	}
	
	public function getSessionSalt() {
		if ($this->session !== null) {
			return $this->session->getSessionSalt();
		}
		
		return null;
	}
	
	public function getSession() {
		return $this->session;
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
	
	public function getCacheTTL() {
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
	public static function getUserIP() {
		if (!isset($_SERVER['REMOTE_ADDR'])) {
			$ip = 'console';
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] !== '') {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		
		return $ip;
	}
	
	public static function getUserAgent() {
		return $_SERVER['HTTP_USER_AGENT'] . $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	}
	
	public static function getCallingServerName() {
		$reservedSubDomains = [
			'compare',
			'hardware',
			'order',
		];
		$serverName = $_SERVER['SERVER_NAME'];
		$isReservedServerName = false;
		foreach ($reservedSubDomains as $sub_domain) {
			if (TypeTransform::startsWith($serverName, $sub_domain) && TypeTransform::endsWith($serverName, 'astel.be')) {
				$isReservedServerName = true;
				break;
			}
		}
		if ($isReservedServerName) {
			$serverName = str_replace('https://', '', str_replace('http://', '', $_SERVER['HTTP_ORIGIN']));
		}
		
		return $serverName;
	}
	
	/**
	 * @return string
	 */
	public static function getUniqueVisitorKey($salt = '') {
		$data = [
			'domain' => self::getCallingServerName(),
			'user_agent' => self::getUserAgent(),
			'session_salt' => $salt,
		];
		
		return self::getUniqueVisitorKeyFromData($data);
	}
	
	public static function getUniqueVisitorKeyFromData(array $data) {
		return md5(Hash::get($data, 'domain', '') . Hash::get($data, 'user_agent', '') . Hash::get($data, 'session_salt', ''));
	}
	
	public static function getReferrer() {
		return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
	
	/**
	 * @param string $token
	 */
	public function setPartnerReferralId($partnerId) {
		$this->partner_referral_id = $partnerId;
	}

  /**
   * @return bool
   */
  public function getHasUserCookieConsent() {
    return $this->has_user_cookie_consent;
  }

  /**
   * @param bool
   */
  public function setHasUserCookieConsent($has_user_cookie_consent) {
    $this->has_user_cookie_consent = $has_user_cookie_consent;
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

	/**
	 * @return string md5 of last_update_time|current_time
	 * Used to deal with css or js files versions
	 */
	public function getVersionData () {
		if ($this->getSession() === null) {
			$version_data = md5(date('mdH'));
		} else {
			$version_data = md5($this->getSession()->sessionGet('website.last_update_time'));
		}
		return $version_data;
	}
}