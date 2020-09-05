<?php

namespace AstelSDK;

use CakeUtility\Hash;

class EmulatedSession {
	
	protected $sessionId = null;
	protected $sessionSalt = null;
	
	public function __construct() {
		$this->sessionInitiate();
	}
	
	public function sessionInitiate() {
		if ($this->sessionId === null) {
			if (!isset($_COOKIE['emulated_session_id'])) {
				// new visitor with new cookie, new session
				$this->setCookieSessionID();
			} else {
				$this->sessionId = $_COOKIE['emulated_session_id'];
				// visitor with already a cookie
			}
		}
	}
	
	private function setCookieSessionID() {
		// cookie domain = this domain without the subdomain
		$cookie_domain = substr($_SERVER['SERVER_NAME'], strpos($_SERVER['SERVER_NAME'], '.'));
		$sessionTimeout = time() + 60 * 60 * 24 * 30;
		$this->sessionSalt = self::generateSalt();
		$this->sessionId = AstelContext::getUniqueVisitorKey($this->sessionSalt);
		setcookie('emulated_session_id', $this->sessionId, $sessionTimeout, '/', $cookie_domain, true, false);
	}
	
	public function destroySession() {
		if (isset($_COOKIE['emulated_session_id'])) {
			unset($_COOKIE['emulated_session_id']);
			// reset the session ID to a new ID
			$this->setCookieSessionID();
			
			return true;
		}
		
		return false;
	}
	
	public function isSessionValid(array $session) {
		$sessionSalt = Hash::get($session, 'session_salt');
		$sessionId = Hash::get($session, 'token');
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