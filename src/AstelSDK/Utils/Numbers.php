<?php
namespace AstelSDK\Utils;

class Numbers {
	public static function priceDisplayLocale($price) {
		return (SCOPE_LANG == 'NL' ? '€' . $price : $price . ' €');
	}
}