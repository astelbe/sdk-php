<?php
namespace AstelSDK\Utils;

class Numbers {
	public static function priceDisplayLocale($price) {
		// TODO handles multiple language/locale for it
		
		return ($language == 'NL' ? '€' . $price : $price . ' €');
	}
}