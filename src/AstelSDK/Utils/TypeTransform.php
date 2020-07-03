<?php

namespace AstelSDK\Utils;
use CakeUtility\Hash;
use CakeUtility\Set;

class TypeTransform {
	
	public static function unserializeRecursive($val) {
		//$pattern = "/.*\{(.*)\}/";
		if (self::is_serialized($val)) {
			$val = trim($val);
			$ret = unserialize($val);
			if (is_array($ret)) {
				foreach ($ret as &$r) {
					$r = self::unserializeRecursive($r);
				}
			} elseif (self::is_serialized($ret)) {
				$ret = self::unserializeRecursive($ret);
			}
			
			return $ret;
		} elseif (is_array($val)) {
			foreach ($val as &$r) {
				$r = self::unserializeRecursive($r);
			}
			
			return $val;
		}
		
		return $val;
	}
	
	// Next two functions taken from a commenter on http://php.net/manual/en/function.unserialize.php
	public static function is_serialized($val) {
		if (!is_string($val)) {
			return false;
		}
		if (trim($val) == "") {
			return false;
		}
		$val = trim($val);
		if (preg_match('/^(i|s|a|o|d):.*{/si', $val) > 0) {
			return true;
		}
		
		return false;
	}
	
	public static function sortTwoLevel($array, $path, $secondPath, $order, $order2) {
		$results = [];
		$firstOrder = Set::sort($array, $path, $order);
		if (isset($secondPath) && $secondPath !== '') {
			$i = 0;
			$toCount = count($firstOrder);
			$currentValue = null;
			$currentValueResultArray = [];
			while ($i < $toCount) {
				$thisCurrentValue = Hash::get($firstOrder, $i . '.' . str_replace('{n}.', '', $path));
				if ($currentValue === null || $thisCurrentValue === $currentValue) {
					//
				} else {
					$currentValueResultArray = Set::sort($currentValueResultArray, $secondPath, $order2);
					$results = array_merge($results, $currentValueResultArray);
					$currentValueResultArray = [];
				}
				$currentValueResultArray[] = $firstOrder[$i];
				$currentValue = $thisCurrentValue;
				$i++;
			}
			if (!empty($currentValueResultArray)) {
				$currentValueResultArray = Set::sort($currentValueResultArray, $secondPath, $order2);
				$results = array_merge($results, $currentValueResultArray);
			}
		} else {
			$results = $firstOrder;
		}
		
		return $results;
	}
}