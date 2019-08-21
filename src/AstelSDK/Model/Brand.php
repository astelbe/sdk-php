<?php

namespace AstelSDK\Model;
use CakeUtility\Hash;

class Brand extends SDKModel {
	
	protected $associated_instance_name = '\AstelSDK\API\Brand';

	/**
	 * Get values for 5 stars quality score
	 *
	 * @param array $brand returned by API
	 *
	 * @return array of values for full Stars, Half stars and empty stars
	 */
	public function getQualityStarsValue(array $brand){
		$score = 0;
		$i = 0;
		foreach(Hash::get($brand, 'quality_score', []) as $k => $play){
			if((int)$play !== 0){

				$score += (int) $play;
				$i ++;
			}
		}
		if($i > 0) {
			// Get value on 5
			$score = round($score / $i *0.05, 1);
		}

		$fullStars = floor($score);
		$halfStars = ceil($score) - $fullStars;
		$emptyStars = 5 - $fullStars - $halfStars;

		return [$fullStars, $halfStars, $emptyStars];
	}
	
	public function numberPlayTelecom(array $brand){
		$nbrPlay = 0;
		if (Hash::get($brand, 'is_play.is_mobile', false)) {
			$nbrPlay = $nbrPlay + 1;
		}
		if (Hash::get($brand, 'is_play.is_fix', false)) {
			$nbrPlay = $nbrPlay + 1;
		}
		if (Hash::get($brand, 'is_play.is_internet', false)) {
			$nbrPlay = $nbrPlay + 1;
		}
		if (Hash::get($brand, 'is_play.is_tv', false)) {
			$nbrPlay = $nbrPlay + 1;
		}
		return $nbrPlay;
	}
	
	public function isType(array $brand, $type) {
		if (null === $brand || empty($brand)) {
			return false;
		}
		if (strtoupper($type) === 'M' && Hash::get($brand, 'is_play.is_mobile')) {
			return true;
		}
		if (strtoupper($type) === 'F' && Hash::get($brand, 'is_play.is_fix')) {
			return true;
		}
		if (strtoupper($type) === 'I' && Hash::get($brand, 'is_play.is_internet')) {
			return true;
		}
		if (strtoupper($type) === 'T' && Hash::get($brand, 'is_play.is_tv')) {
			return true;
		}
		if (strtoupper($type) === 'H' && Hash::get($brand, 'is_play.is_hardware')) {
			return true;
		}
		
		return false;
	}
	
}