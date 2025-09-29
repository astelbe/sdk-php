<?php

namespace AstelSDK;

use AstelSDK\AstelContext;
use CakeUtility\Hash;
use AstelSDK\Model\WebsiteConnection;

class EmulatedSession {
	
	protected $context;
	protected $sessionId = null;
	protected $sessionSalt = null;
	protected $WebsiteConnectionModel;
	protected $connection;
	
	protected $navigatorAcceptCookies = true;
	
	public function __construct(AstelContext $context) {
		$this->context = $context;
		$this->WebsiteConnectionModel = WebsiteConnection::getInstance();
		$this->sessionInitiate();
	}
	
	protected function sessionInitiate() {
		if (!isset($_COOKIE['session_id'])) {
			// new visitor with new cookie, new session to create directly via websiteconnection
			$this->setCookieSessionID();
			if (!isset($_COOKIE['session_id'])) {
				$this->navigatorAcceptCookies = false;
				$userAgent = AstelContext::getUserAgent();
				$ignoreUserAgentContain = ['Amazon-Route53-Health-Check-Service', 'bingbot', 'SemrushBot', 'Googlebot', 'Adsbot', 'Trident', 'MagpieRSS', 'UptimeRobot', 'MojeekBot','YandexBot'];
				$isIgnored = false;
				foreach ($ignoreUserAgentContain as $ignored) {
					if (strpos($userAgent, $ignored) !== false) {
						$isIgnored = true;
						break;
					}
				}
				if (!$isIgnored) {
					//$this->context->log('The customer has deactivated his cookies - User Agent: ' . $userAgent);
				}
			}
		} else {
			$this->sessionId = $_COOKIE['session_id'];
			// visitor with already a cookie, we then check the session via websiteconnection
		}
		try {
			$websiteConnectData = $this->retrieveWebsiteConnection();
			if ($websiteConnectData === null || empty($websiteConnectData) || !self::isSessionValid($websiteConnectData)) {
				// Needs to recreate session, invalid session
				$this->destroyRestartSession();
				$websiteConnectData = $this->retrieveWebsiteConnection();
			}
			$this->connection = $websiteConnectData;
			$collectedSessionId = Hash::get($websiteConnectData, 'session_id');
			$this->sessionSalt = Hash::get($websiteConnectData, 'session_salt');
			if ($collectedSessionId !== $this->sessionId) {
				$this->setCookieSessionID($this->sessionSalt, $collectedSessionId);
			}
		} catch (\Exception $exception) {
			$this->context->log($exception->getMessage());
		}
	}
	
	public static $acceptingCookies = null;
	
	public static function isNavigatorAcceptingCookies() {
		if(isset($_COOKIE['cookieconsent_status']) && $_COOKIE['cookieconsent_status'] !== ''){
			return true;
		} else {
			// Only set cookie if headers haven't been sent yet
			if (!headers_sent()) {
				setcookie('cookieconsent_status', 'unknown', time() + 60 * 60,'/','',true,false);
			}
		}
		
		return isset($_COOKIE['cookieconsent_status']) && $_COOKIE['cookieconsent_status'] !== '';
	}
	
	public function getConnectionData() {
		return $this->connection;
	}
	
	public function sessionGet($path = '', $default = null) {
		if ($path === '') {
			if ($this->connection === null) {
				return [];
			}
			
			return $this->connection;
		}
		if ($this->connection === null) {
			return null;
		}
		
		return Hash::get($this->connection, $path, $default);
	}
	
	public function sessionRefresh($params = []) {
		$this->connection = $this->retrieveWebsiteConnection($params);
	}
	
	protected function retrieveWebsiteConnection($params = []) {
		try {
			$connectParams = [];
			$connectParams['session_id'] = $this->sessionId;
			if ($this->sessionSalt !== null) {
				$connectParams['session_salt'] = $this->sessionSalt;
			}
			if (isset($params) && !empty($params)) {
				$params = array_merge($params, $connectParams);
			} else {
				$params = $connectParams;
			}
			
			return $this->WebsiteConnectionModel->find('first', $params);
		} catch (Exception $e) {
			$this->context->log('Error retrieving Website Connection');
		}
		
		return [];
	}
	
	protected function setCookieSessionID($session_salt = '', $session_id = '') {
		// cookie domain = this domain without the subdomain
		$cookie_domain = substr($_SERVER['SERVER_NAME'], strpos($_SERVER['SERVER_NAME'], '.'));
		$sessionTimeout = time() + 60 * 60 * 24 * 30;
		if ($session_salt === '') {
			$this->sessionSalt = self::generateSalt();
		} else {
			$this->sessionSalt = $session_salt;
		}
		if ($session_id === '') {
			$this->sessionId = AstelContext::getUniqueVisitorKey($this->sessionSalt);
		} else {
			$this->sessionId = $session_id;
		}
		setcookie('session_id', $this->sessionId, $sessionTimeout, '/', $cookie_domain, true, false);
	}
	
	protected function destroyRestartSession() {
		if (isset($_COOKIE['session_id'])) {
			unset($_COOKIE['session_id']);
			// reset the session ID to a new ID
			$this->setCookieSessionID();
			
			return true;
		}
		
		return false;
	}
	
	public static function isSessionValid(array $session, $currentData = []) {
		$sessionSalt = Hash::get($session, 'session_salt');
		$sessionId = Hash::get($session, 'session_id');
		if (empty($currentData)) {
			$sessionIdShouldBe = AstelContext::getUniqueVisitorKey($sessionSalt);
		} else {
			$sessionIdShouldBe = AstelContext::getUniqueVisitorKeyFromData($currentData);
		}
		return $sessionId === $sessionIdShouldBe;
	}
	
	public static function generateSalt() {
		return md5(rand(0, getrandmax()) . uniqid('', true));
	}
	
	public function getSessionSalt() {
		return $this->sessionSalt;
	}
	
	public function getSessionID() {
		return $this->sessionId;
	}
	
}
