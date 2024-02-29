<?php

namespace AstelSDK\WebIntegration;

use AstelSDK\Utils\URL;
use AstelShared\Typeahead;

class Comparator extends AbstractWebIntegration {
	
	public function getCSSList($allRequired = true) {
		$cssList = [];
		
		if ($allRequired) {
			$cssList[] = 'https://cdn' . $this->context->getEnv() . '.astel.be/libs/bootstrap/4.3.1/css/bootstrap.min.css';
			$cssList[] = 'https://cdn' . $this->context->getEnv() . '.astel.be/libs/font-awesome/4.7.0/css/font-awesome.min.css';
		}
		$cssList[] = 'https://compare' . $this->context->getEnv() . '.astel.be/css/compare/comparator.css?v=' . $this->context->getVersion();
		
		return $cssList;
	}
	
	public function getJSList() {
		$Typeahead = Typeahead::getInstance();
		$typeahead_js = $Typeahead->getJsList();
		$comparator_js = [
			'https://files' . $this->context->getEnv() . '.astel.be/DJs/astelContentInjector.js?v=' . $this->context->getVersion(),
			'https://compare' . $this->context->getEnv() . '.astel.be/comparator/inject.js?v=' . $this->context->getVersion(),
		];
		return array_merge($typeahead_js, $comparator_js);
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
	
	public function getScriptLoadComparator($title = null) {
		global $_GET;

		$getParams = [];

		// Process $_GET params
        // Be aware that $_GET param names are not the same in comparator scripts
		$defaultGET = [
			'mobile' => 0,
			'fixe' => 0,
			'internet' => 0,
			'tv' => 0,
			'illimite' => 0,
			'3g' => 0,
			'type' => 'fix',
			'code_postal' => '',
			'order_type' => 2,
			'usage' => 1,
			'is_student' => 0,
		];
		if (!empty($_GET)) {
			$_GET = array_merge($defaultGET, $_GET);
		}

		// Postal code : accept 'code_postal', 'postal_code' or 'postal_code_id' if id is given
		if (isset($_GET['code_postal']) && $_GET['code_postal'] !== '') {
			$getParams['postal_code'] = $_GET['code_postal'];
		}
		if (isset($_GET['postal_code']) && $_GET['postal_code'] !== '') {
			$getParams['postal_code'] = $_GET['postal_code'];
		}
		if (isset($_GET['postal_code_id']) && $_GET['postal_code_id'] !== '') {
			$getParams['postal_code_id'] = $_GET['postal_code_id'];
		}

		// Mobile
		if (isset($_GET['mobile']) && $_GET['mobile'] !== '') {
			$is_mobile = (int)$_GET['mobile'];
			$getParams['is_mobile'] = 0;
			if ($is_mobile == 1) {
				$getParams['is_mobile'] = 1;
				if (isset($_GET['mobile_small_qt']) && $_GET['mobile_small_qt'] >= 0) {
					$getParams['mobile_small_qt'] = (int)$_GET['mobile_small_qt'];
				}
				if (isset($_GET['mobile_regular_qt']) && $_GET['mobile_regular_qt'] >= 0) {
					$getParams['mobile_regular_qt'] = (int)$_GET['mobile_regular_qt'];
				}
				if (isset($_GET['mobile_heavy_qt']) && $_GET['mobile_heavy_qt'] >= 0) {
					$getParams['mobile_heavy_qt'] = (int)$_GET['mobile_heavy_qt'];
				}
				if (isset($_GET['mobile_heavy_int_qt']) && $_GET['mobile_heavy_int_qt'] >= 0) {
					$getParams['mobile_heavy_int_qt'] = (int)$_GET['mobile_heavy_int_qt'];
				}
			}
			
		}
		// Fix
		// Can use 'fixe' or 'fix'. Comp V2 code send url with 'fix'
        $is_fix = $_GET['fixe'] ?: $_GET['fix'] ?: false;
        $is_fix = (int)$is_fix;
		if($is_fix !== false) {
			$getParams['is_fix'] = 0;
			if ($is_fix > 0) {
				$getParams['is_fix'] = 1;
				switch ($is_fix) {
					case 2:
						$getParams['fix_usage'] = 'MEDIUM';
						break;
					case 3:
						$getParams['fix_usage'] = 'HEAVY';
						break;
					case 4:
						$getParams['fix_usage'] = 'HEAVYINT';
						break;
				}
			}
		}

		// Internet
		if (isset($_GET['internet']) && $_GET['internet'] !== '') {
			$is_internet = (int)$_GET['internet'];
			$getParams['is_internet'] = 0;
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
			}
		}

		// Tv
		if (isset($_GET['tv']) && $_GET['tv'] !== '') {
			$is_tv = (int)$_GET['tv'];
			$getParams['is_tv'] = 0;
			if ($is_tv > 0) {
				$getParams['is_tv'] = 1;
                switch ($is_tv) {
                    case 2:
                        $getParams['tv_usage'] = 'WITH_DECODER';
                        break;
                    case 3:
                        $getParams['tv_usage'] = 'FROM_APPLICATION';
                        break;
                }
			}
		}

		// Order by
		if (isset($_GET['order_type']) && $_GET['order_type'] !== '' && is_numeric($_GET['order_type'])) {
			// 0 = order by price
			// 1 = order by quality
			// 2 = order by quality/price
			// 3 = order by delay
			// 4 = order by savings
			$order_type = (int)$_GET['order_type'];
			$getParams['order_type'] = $order_type;
		}

		// Is student filter
		if (isset($_GET['is_student']) && $_GET['is_student'] !== '' && is_numeric($_GET['is_student'])) {
			$isStudent = (int)$_GET['is_student']; 
			$getParams['is_student'] = $isStudent;
		}  

		if (isset($_GET['is_static_display'])) {
			$getParams['is_static_display'] = $_GET['is_static_display'];
		}
		if (isset($_GET['username'])) {
			$getParams['username'] = $_GET['username'];
		}

		if (isset($_GET['partnerID'])) {
			$getParams['partnerID'] = $_GET['partnerID'];
		}

		$getParams['page_title'] = $title;
		$paramsURL = $this->getParamsUrl($getParams);

    $encryptionKey = $this->context->getEcryptionKey();
		return '<script>
			getAstelComparator("comparatorDiv", "' . $this->context->getLanguage() . '", "' . $paramsURL . '");
		</script>';
	}
	
	public function getScriptLoadComparatorParameterBar() {
		
		$paramsURL = $this->getParamsUrl();
		
		return '<script>
			getAstelStandaloneParameterBar("comparatorDiv", "' . $this->context->getLanguage() . '", "' . $paramsURL . '");
		</script>';
	}
	
	private function getParamsUrl($getParams = []) {
		$getParams['page_url'] = $this->getPageURL();
		$is_professional = ($this->context->getisPrivate() === 1 || $this->context->getisPrivate() === true || $this->context->getisPrivate() === null) ? 0 : 1;
		$getParams['is_professional'] = $is_professional;
		$getParams['session_id'] = $this->context->getSessionID();
		$serialize = serialize($getParams);
		$paramsURL = URL::base64url_encode($serialize);
		
		return $paramsURL;
	}
	
	public function getBodyLoadHtml() {
		return '<div id="comparatorDiv">
				<div class="loadingImg text-center">
					<div class="spinner-border text-blue" style="width: 5rem; height: 5rem;" role="status">
						<span class="sr-only">Loading...</span>
					</div>
					' . $this->txtToDisplayNoCookieTechnicalIssue() . '
				</div>
			</div>';
	}
}