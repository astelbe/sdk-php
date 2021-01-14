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
	
	public static function displayVolumeGBFromMB($volumeinMB) {
		if (is_numeric($volumeinMB)) {
			$value = round($volumeinMB / 1000, 1);
			
			if (is_numeric($value) && floor($value) != $value) {
				// is Decimal
				return number_format($value, 1, ',', '.');
			} else {
				return number_format($value, 0, ',', '.');
			}
		}
		
		return '';
	}
	
}