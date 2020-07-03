<?php

namespace AstelSDK\Utils;

class HALOperations {
	
	/**
	 * @param array $resultArray
	 *
	 * @return array
	 */
	public static function interpretHALLogicToSimpleArray($resultArray) {
		if (isset($resultArray['_embedded']['items'])) {
			// For Collection
			if (empty($resultArray['_embedded']['items'])) {
				$resultArray = [];
			} else {
				$res = [];
				foreach ($resultArray['_embedded']['items'] as $tmpID => $result) {
					$res[$tmpID] = self::interpretHALLogicToSimpleArray($result);
				}
				$resultArray = $res;
			}
		} else {
			// For Item
			if (isset($resultArray['_embedded']) && !empty($resultArray['_embedded'])) {
				foreach ($resultArray['_embedded'] as $embeddedModelName => $embeddedValue) {
					$resultArray[$embeddedModelName] = self::interpretHALLogicToSimpleArray($embeddedValue);
				}
				unset($resultArray['_embedded']);
			}
			unset($resultArray['_links']);
		}
		
		return $resultArray;
	}
	
}