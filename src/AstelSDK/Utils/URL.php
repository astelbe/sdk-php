<?php

namespace AstelSDK\Utils;

class URL {
	
	/**
	 * Default map of accented and special characters to ASCII characters
	 *
	 * @var array
	 */
	protected static $_transliteration = [
		'/À|Á|Â|Ã|Å|Ǻ|Ā|Ă|Ą|Ǎ/' => 'A',
		'/Æ|Ǽ/' => 'AE',
		'/Ä/' => 'Ae',
		'/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
		'/Ð|Ď|Đ/' => 'D',
		'/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě/' => 'E',
		'/Ĝ|Ğ|Ġ|Ģ|Ґ/' => 'G',
		'/Ĥ|Ħ/' => 'H',
		'/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|І/' => 'I',
		'/Ĳ/' => 'IJ',
		'/Ĵ/' => 'J',
		'/Ķ/' => 'K',
		'/Ĺ|Ļ|Ľ|Ŀ|Ł/' => 'L',
		'/Ñ|Ń|Ņ|Ň/' => 'N',
		'/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ/' => 'O',
		'/Œ/' => 'OE',
		'/Ö/' => 'Oe',
		'/Ŕ|Ŗ|Ř/' => 'R',
		'/Ś|Ŝ|Ş|Ș|Š/' => 'S',
		'/ẞ/' => 'SS',
		'/Ţ|Ț|Ť|Ŧ/' => 'T',
		'/Þ/' => 'TH',
		'/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ/' => 'U',
		'/Ü/' => 'Ue',
		'/Ŵ/' => 'W',
		'/Ý|Ÿ|Ŷ/' => 'Y',
		'/Є/' => 'Ye',
		'/Ї/' => 'Yi',
		'/Ź|Ż|Ž/' => 'Z',
		'/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª/' => 'a',
		'/ä|æ|ǽ/' => 'ae',
		'/ç|ć|ĉ|ċ|č/' => 'c',
		'/ð|ď|đ/' => 'd',
		'/è|é|ê|ë|ē|ĕ|ė|ę|ě/' => 'e',
		'/ƒ/' => 'f',
		'/ĝ|ğ|ġ|ģ|ґ/' => 'g',
		'/ĥ|ħ/' => 'h',
		'/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|і/' => 'i',
		'/ĳ/' => 'ij',
		'/ĵ/' => 'j',
		'/ķ/' => 'k',
		'/ĺ|ļ|ľ|ŀ|ł/' => 'l',
		'/ñ|ń|ņ|ň|ŉ/' => 'n',
		'/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º/' => 'o',
		'/ö|œ/' => 'oe',
		'/ŕ|ŗ|ř/' => 'r',
		'/ś|ŝ|ş|ș|š|ſ/' => 's',
		'/ß/' => 'ss',
		'/ţ|ț|ť|ŧ/' => 't',
		'/þ/' => 'th',
		'/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ/' => 'u',
		'/ü/' => 'ue',
		'/ŵ/' => 'w',
		'/ý|ÿ|ŷ/' => 'y',
		'/є/' => 'ye',
		'/ї/' => 'yi',
		'/ź|ż|ž/' => 'z',
	];
	
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
	 * Returns a string with all spaces and underscores converted to -, accented
	 * characters converted to non-accented characters, and non word characters removed.
	 *
	 * @param string $string the string you want to slug
	 *
	 * @return string
	 * @author Based on cakephp slug function
	 */
	public static function slug($string) {
		$string = trim($string);
		$withoutUnderscore = str_replace('_', '-', $string);
		$quotedReplacement = preg_quote('-', '/');
		
		$merge = [
			'/[^\s\p{Zs}\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu' => ' ',
			'/[\s\p{Zs}]+/mu' => '-',
			sprintf('/^[%s]+|[%s]+$/', $quotedReplacement, $quotedReplacement) => '',
		];
		
		$map = static::$_transliteration + $merge;
		
		return strtolower(preg_replace(array_keys($map), array_values($map), $withoutUnderscore));
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