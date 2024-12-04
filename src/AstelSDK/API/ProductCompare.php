<?php

namespace AstelSDK\API;

use AstelSDK\Exception\ValidationErrorException;
use CakeUtility\Hash;

class ProductCompare extends APIModel {
	
	protected $default_params = [];
	
	public function clear() {
		$this->default_params = [];
		$this->cacheResults = []; // because we use default params, not direct find params
	}
	
	protected function prepare() {
		$this->default_params = [
			'language' => $this->context->getLanguage(),
			'is_mobile' => 0,
			'is_fix' => 0,
			'is_internet' => 0,
			'is_tv' => 0,
			'order' => 'price',
		];
		if (!$this->context->getIsPrivate()) {
			$this->default_params['is_professional'] = 1;
		} else {
			$this->default_params['is_professional'] = 0;
		}
		
		return $this->default_params;
	}
	
	public function paramMobile($is_mobile = true, $mobile_small_qt = 0, $mobile_regular_qt = 0, $mobile_heavy_qt = 0, $mobile_heavy_int_qt = 0) {
		if (empty($this->default_params)) {
			$this->prepare();
		}
		if ($is_mobile) {
			$this->default_params['is_mobile'] = 1;
			if ($mobile_small_qt > 0) {
				$this->default_params['mobile_small_qt'] = $mobile_small_qt;
			} else {
				unset($this->default_params['mobile_small_qt']);
			}
			if ($mobile_regular_qt > 0) {
				$this->default_params['mobile_regular_qt'] = $mobile_regular_qt;
			} else {
				unset($this->default_params['mobile_regular_qt']);
			}
			if ($mobile_heavy_qt > 0) {
				$this->default_params['mobile_heavy_qt'] = $mobile_heavy_qt;
			} else {
				unset($this->default_params['mobile_heavy_qt']);
			}
			if ($mobile_heavy_int_qt > 0) {
				$this->default_params['mobile_heavy_int_qt'] = $mobile_heavy_int_qt;
			} else {
				unset($this->default_params['mobile_heavy_int_qt']);
			}
		} else {
			$this->default_params['is_mobile'] = 0;
			unset($this->default_params['mobile_small_qt'], $this->default_params['mobile_regular_qt'], $this->default_params['mobile_heavy_qt'], $this->default_params['mobile_heavy_int_qt']);
		}
	}
	
	public function paramFix($is_fix = true, $usage = 'MEDIUM') {
		if (empty($this->default_params)) {
			$this->prepare();
		}
		if ($is_fix) {
			$this->default_params['is_fix'] = 1;
			if ($usage !== 'MEDIUM' && $usage !== 'HEAVY' && $usage !== 'HEAVYINT') {
				throw new ValidationErrorException('Validations error during the param validation. Please correct input. Fix usage', 400);
			}
			$this->default_params['fix_usage'] = $usage;
		} else {
			$this->default_params['is_fix'] = 0;
			unset($this->default_params['fix_usage']);
		}
	}
	
	public function paramInternet($is_internet = true, $usage = 'MEDIUM') {
		if (empty($this->default_params)) {
			$this->prepare();
		}
		if ($is_internet) {
			$this->default_params['is_internet'] = 1;
			if ($usage !== 'SMALL' && $usage !== 'MEDIUM' && $usage !== 'HEAVY') {
				throw new ValidationErrorException('Validations error during the param validation. Please correct input. Internet Usage.', 400);
			}
			$this->default_params['internet_usage'] = $usage;
		} else {
			$this->default_params['is_internet'] = 0;
			unset($this->default_params['internet_usage']);
		}
	}
	
	public function paramTv($is_tv = true, $usage = 'WITH_DECODER') {
		if (empty($this->default_params)) {
			$this->prepare();
		}
		if ($is_tv) {
			$this->default_params['is_tv'] = 1;
			if ($usage !== 'WITH_DECODER' && $usage !== 'FROM_APPLICATION') {
				throw new ValidationErrorException('Validations error during the param validation. Please correct input. Tv Usage.', 400);
			}
			$this->default_params['tv_usage'] = $usage;
		} else {
			$this->default_params['is_tv'] = 0;
			unset($this->default_params['tv_usage']);
		}
	}
	
	public function paramPostalCodeID($postal_code_id) {
		if (empty($this->default_params)) {
			$this->prepare();
		}
		$this->default_params['postal_code_id'] = $postal_code_id;
	}
	
	public function paramBrandID($brand_id) {
		if (empty($this->default_params)) {
			$this->prepare();
		}
		$this->default_params['brand_id'] = $brand_id;
	}
	
	public function paramIsProfessional() {
		if (empty($this->default_params)) {
			$this->prepare();
		}
		$this->default_params['is_professional'] = 1;
	}
	
	public function paramIsPrivate() {
		if (empty($this->default_params)) {
			$this->prepare();
		}
		$this->default_params['is_professional'] = 0;
	}
	
	public function paramLanguage($language = 'FR') {
		if (empty($this->default_params)) {
			$this->prepare();
		}
		$this->default_params['language'] = $language;
	}
	
	public function paramLimit($limit) {
		if (empty($this->default_params)) {
			$this->prepare();
		}
		$this->default_params['count'] = $limit;
	}

  /**
   * Prepare requete from crontroller
   * @param $params array
   * Should contain order_by, usages, 
   */
	public function prepareCompareRequest($params) {
    // Order
    if (isset($params['order_by'])) {
      $this->setOrder($params['order_by']);
    }
    // Usages
    if (isset($params['usages'])) {
      $this->setUsages($params['usages']);
    }
    // Limit
    if (isset($params['results_count'])) {
      $this->paramLimit($params['results_count']);
    }
    debug($this->default_params);
  }

	public function setOrder($orderParam) {
		switch ($orderParam) {
		  case 'quality_price':
			$this->paramOrderByQualityPrice();
			break;
		  case 'price':
			$this->paramOrderByPrice();
			break;
		  case 'quality':
			$this->paramOrderByQuality();
			break;
		  case 'delay':
			$this->paramOrderByDelay();
			break;
		}
	}

  public function setUsages($usages) {
    foreach ($usages as $play_type => $usage) {
      switch ($play_type) {
        case 'mobile':
          $small = Hash::get($usage, 'SMALL', 0);
          $medium = Hash::get($usage, 'MEDIUM', 0);
          $heavy = Hash::get($usage, 'HEAVY', 0);
          $heavy_int = Hash::get($usage, 'HEAVY_INT', 0);
          $this->paramMobile(true, $small, $medium, $heavy, $heavy_int);
          break;
        case 'internet':
          $this->paramInternet(true, $usage);
          break;
        case 'tv':
          $this->paramTv(true, $usage);
          break;
        case 'fix':
          if ($usage == 1) {
            $this->paramFix(true);
          }
          break;
      }
    }
    // debug($this->default_params);
  }
	
	public function paramOrderByPrice() {
		if (empty($this->default_params)) {
			$this->prepare();
		}
		$this->default_params['order'] = 'price';
	}
	
	public function paramOrderByQuality() {
		if (empty($this->default_params)) {
			$this->prepare();
		}
		$this->default_params['order'] = 'quality';
	}
	
	public function paramOrderByQualityPrice() {
		if (empty($this->default_params)) {
			$this->prepare();
		}
		$this->default_params['order'] = 'qualityprice';
	}
	
	public function paramOrderByDelay() {
		if (empty($this->default_params)) {
			$this->prepare();
		}
		$this->default_params['order'] = 'delay';
	}
	
	protected function getAll(array $params = []) {
		$query = $this->newQuery();
		$query->setUrl('v2_00/product/compare');
		if (empty($this->default_params)) {
			$this->prepare();
		}
		$params = Hash::merge($this->default_params, $params);
		$query->addGETParams($params);
		
		return $query->exec();
	}
}