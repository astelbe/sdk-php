<?php

namespace AstelSDK\Utils;

class VatCalculation extends Singleton {
	public $vat_rate;
	public $default_vat_rate = 0.21; // Belgian VAT Rate
	
	public function __construct($vat_rate = null) {
		$this->setVatRate($vat_rate);
	}
	
	public function getVatRate() {
		return $this->vat_rate;
	}
	
	public function setVatRate($vat_rate) {
		if ($vat_rate !== null) {
			$this->vat_rate = $vat_rate;
		} else {
			$this->vat_rate = $this->default_vat_rate;
		}
	}
	
	/**
	 * @param $price
	 * @param $is_price_htva
	 * @param $force_price_htva
	 * @param int $precision
	 *
	 * @return false|float
	 */
	public function calculatePriceForceHTVA($price, $is_price_htva, $force_price_htva, $precision = 2) {
		if ($is_price_htva == $force_price_htva) {
			// do nothing and return price
			$price = round($price, $precision);
		} elseif ($force_price_htva == 1) {
			// Transform TVAC -> HTVA
			$price = $this->calculateVATExcluded($price, $is_price_htva, $precision);
		} elseif ($force_price_htva == 0) {
			// Transform HTVA -> TVAC
			$price = $this->calculateVATIncluded($price, $is_price_htva, $precision);
		}
		
		return $price;
	}
	
	/**
	* Calculate the VAT included price of the given parameter
	*/
	public function calculateVATIncluded($price, $is_htva = null, $precision = 2) {
		$rate = 1;
		if ($is_htva == 1) {
			$rate = $rate + $this->vat_rate;
		}
		
		return round($price * $rate, $precision);
	}
	
	/**
	* Calculate the VAT excluded price of the given parameter
	*/
	public function calculateVATExcluded($price, $is_htva = null, $precision = 2) {
		$rate = 1;
		if ($is_htva == 0) {
			$rate = 100 / (($this->vat_rate * 100) + 100);
		}
		
		return round($price * $rate, $precision);
	}
}