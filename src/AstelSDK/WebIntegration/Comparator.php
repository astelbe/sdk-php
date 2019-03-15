<?php

namespace AstelSDK\WebIntegration;

use AstelSDK\QueryManager;

class Comparator extends QueryManager {
	
	public function getCSSList($allRequired = true) {
		$cssList = [];
		
		if ($allRequired) {
			$cssList[] = 'https://cdn' . $this->context->getEnv() . '.astel.be/libs/bootstrap/4.0.0/css/bootstrap.min.css';
			$cssList[] = 'https://cdn' . $this->context->getEnv() . '.astel.be/libs/font-awesome/4.7.0/css/font-awesome.min.css';
		}
		$cssList[] = 'https://compare' . $this->context->getEnv() . '.astel.be/css/compare/comparator.css?v=' . $this->context->getVersion();
		
		return $cssList;
	}
	
	public function getJSList() {
		return [
			'https://files' . $this->context->getEnv() . '.astel.be/DJs/astelContentInjector.js?v=' .
			$this->context->getVersion(),
			'https://compare' . $this->context->getEnv() . '.astel.be/comparator/inject.js?v=' . $this->context->getVersion(),
			//'https://cdn.astel.be/libs/bootstrap/4.0.0/js/bootstrap.min.js'
		];
	}
	
	public function getCSS($allRequired = true) {
		$out = '';
		$cssList = $this->getCSSList($allRequired);
		foreach ($cssList as $css) {
			$out .= '<link rel="stylesheet" href="' . $css . '" />';
		}
		
		return $out;
	}
	
	public function getJS() {
		$out = '';
		$jsList = $this->getJSList();
		foreach ($jsList as $js) {
			$out .= '<script type="text/javascript" src="' . $js . '"></script>';
		}
		
		return $out;
	}
	
	public function getScriptLoadComparator() {
		global $_GET;
		$getParams = [];
		if (isset($_GET['code_postal']) && $_GET['code_postal'] != '') {
			$getParams['postal_code'] = $_GET['code_postal'];
		}
		if (isset($_GET['postal_code']) && $_GET['postal_code'] != '') {
			$getParams['postal_code'] = $_GET['postal_code'];
		}
		if (isset($_GET['mobile']) && $_GET['mobile'] != '') {
			$is_mobile = (int)$_GET['mobile'];
			if ($is_mobile == 1) {
				$getParams['is_mobile'] = 1;
				if (isset($_GET['mobile_small_qt']) && $_GET['mobile_small_qt'] > 0) {
					$getParams['mobile_small_qt'] = $_GET['mobile_small_qt'];
				}
				if (isset($_GET['mobile_regular_qt']) && $_GET['mobile_regular_qt'] > 0) {
					$getParams['mobile_regular_qt'] = $_GET['mobile_regular_qt'];
				}
				if (isset($_GET['mobile_heavy_qt']) && $_GET['mobile_heavy_qt'] > 0) {
					$getParams['mobile_heavy_qt'] = $_GET['mobile_heavy_qt'];
				}
				if (isset($_GET['mobile_heavy_int_qt']) && $_GET['mobile_heavy_int_qt'] > 0) {
					$getParams['mobile_heavy_int_qt'] = $_GET['mobile_heavy_int_qt'];
				}
			} else {
				$getParams['is_mobile'] = 0;
			}
		}
		if (isset($_GET['fixe']) && $_GET['fixe'] != '') {
			$is_fix = (int)$_GET['fixe'];
			if ($is_fix > 0) {
				$getParams['is_fix'] = 1;
				switch ($is_fix) {
					case 2:
						$getParams['fix_usage'] = 'MEDIUM';
						break;
					case 3:
						$getParams['fix_usage'] = 'HEAVY';
						break;
					case 5:
						$getParams['fix_usage'] = 'HEAVYINT';
						break;
				}
			} else {
				$getParams['is_fix'] = 0;
			}
		}
		if (isset($_GET['internet']) && $_GET['internet'] != '') {
			$is_internet = (int)$_GET['internet'];
			if ($is_internet > 0) {
				$getParams['is_internet'] = 1;
				switch ($is_internet) {
					case 2:
						$getParams['internet_usage'] = 'SMALL';
						break;
					case 3:
						$getParams['internet_usage'] = 'MEDIUM';
						break;
					case 4:
						$getParams['internet_usage'] = 'HEAVY';
						break;
				}
			} else {
				$getParams['is_internet'] = 0;
			}
		}
		if (isset($_GET['tv']) && $_GET['tv'] != '') {
			$is_tv = (int)$_GET['tv'];
			if ($is_tv > 0) {
				$getParams['is_tv'] = 1;
			} else {
				$getParams['is_tv'] = 0;
			}
		}
		if (isset($_GET['clasQ']) && $_GET['clasQ'] != '') {
			// 0 = order by price
			// 1 = order by quality
			// 2 = order by quality/price
			// 3 = order by delay
			$order_type = (int)$_GET['clasQ'];
			$getParams['order_type'] = $order_type;
		}
		$paramsURL = urlencode(base64_encode(serialize($getParams)));
		
		return '<script>
			getAstelComparator("comparatorDiv", "' . $this->context->getLanguage() . '", "' . $paramsURL . '");
		</script>';
	}
	
	public function getBodyLoadHtml() {
		return '<div id="comparatorDiv">
				<div class="loadingImg text-center">
					<center><img class="loading-transparent"
					src="https://cdn' . $this->context->getEnv() . '.astel.be/assets/astelbefr/img/loading-transparent.gif"></center>
					<!--Image for loading-->
				</div>
			</div>';
	}
}