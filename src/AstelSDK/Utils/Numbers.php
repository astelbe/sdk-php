<?php
namespace AstelSDK\Utils;

class Numbers {
	
	public static function convertMbpsLocale($mbps) {
		$mbps = str_replace('.', ',', $mbps);
		
		return $mbps;
	}
	
	public static function priceDisplayLocale($price) {
		// TODO handles multiple language/locale for it
		
		return ($language == 'NL' ? '€' . $price : $price . ' €');
	}
}