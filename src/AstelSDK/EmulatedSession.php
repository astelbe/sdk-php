<?php

namespace AstelSDK;

use CakeUtility\Hash;
use AstelSDK\Model\WebsiteConnection;

class EmulatedSession {
	
	protected $context;
	protected $sessionId = null;
	protected $sessionSalt = null;
	protected $WebsiteConnectionModel;
	protected $connection;
	
	public function __construct(AstelContext $context) {
		$this->context = $context;
		$this->WebsiteConnectionModel = WebsiteConnection::getInstance();
		$this->sessionInitiate();
	}
	
	protected function sessionInitiate() {
		if (!isset($_COOKIE['session_id'])) {
			// new visitor with new cookie, new session to create directly via websiteconnection
			$this->setCookieSessionID();
		} else {
			$this->sessionId = $_COOKIE['session_id'];
			// visitor with already a cookie, we then check the session via websiteconnection
		}
		try {
			$websiteConnectData = $this->retrieveWebsiteConnection();
			$websiteConnectDataSession = Hash::get($websiteConnectData, 'session');
			if ($websiteConnectDataSession === null || empty($websiteConnectDataSession) || !$this->isSessionValid($websiteConnectDataSession)) {
				// Needs to recreate session, invalid session
				$this->destroyRestartSession();
				$websiteConnectData = $this->retrieveWebsiteConnection();
			}
			$this->connection = $websiteConnectData;
			$this->sessionSalt = Hash::get($websiteConnectData, 'session.session_salt');
			
		} catch (\Exception $exception) {
			$this->context->log($exception->getMessage());
		}
	}
	
	public function getConnectionData() {
		return $this->connection;
	}
	
	protected function retrieveWebsiteConnection() {
		$connectParams = [];
		$connectParams['session_id'] = $this->sessionId;
		if ($this->sessionSalt !== null) {
			$connectParams['session_salt'] = $this->sessionSalt;
		}
		
		return $this->WebsiteConnectionModel->find('first', $connectParams);
	}
	
	protected function setCookieSessionID() {
		// cookie domain = this domain without the subdomain
		$cookie_domain = substr($_SERVER['SERVER_NAME'], strpos($_SERVER['SERVER_NAME'], '.'));
		$sessionTimeout = time() + 60 * 60 * 24 * 30;
		$this->sessionSalt = self::generateSalt();
		$this->sessionId = AstelContext::getUniqueVisitorKey($this->sessionSalt);
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
	
	protected function isSessionValid(array $session) {
		$sessionSalt = Hash::get($session, 'session_salt');
		$sessionId = Hash::get($session, 'session_id');
		$sessionIdShouldBe = AstelContext::getUniqueVisitorKey($sessionSalt);
		
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