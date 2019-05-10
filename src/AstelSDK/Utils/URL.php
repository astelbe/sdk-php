<?php

namespace AstelSDK\Utils;

class URL {
	
	/**
	 * @param $url Full URL
	 *
	 * @return array|bool False if the url is invalid. Array [key->value] for GET elements of the given URL
	 */
	public static function urlToGetParamsArray($url) {
		if ($url === '') {
			return false;
		}
		$params = [];
		$explodedGetParams = explode('?', $url, 2);
		if (isset($explodedGetParams[1])) {
			$params = self::urlToGetParamsArrayHandlesParams($explodedGetParams[1]);
		}
		
		return $params;
	}
	
	/**
	 * @param $urlGetParams
	 *
	 * @return array Transforms a url section containing the get params in an array of key->value
	 */
	public static function urlToGetParamsArrayHandlesParams($urlGetParams) {
		$a = [];
		foreach (explode('&', $urlGetParams) as $q) {
			$p = explode('=', $q, 2);
			$a[$p[0]] = isset ($p[1]) ? $p[1] : '';
		}
		
		return $a;
	}
	
	/**
	 * https://www.php.net/manual/fr/function.base64-encode.php#103849
	 */
	public static function base64url_encode($data) {
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}
	
	public static function base64url_decode($data) {
		return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
	}
	
}