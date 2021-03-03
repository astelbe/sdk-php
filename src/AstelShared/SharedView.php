<?php

namespace AstelShared;

use AstelSDK\Utils\Singleton;

class SharedView extends Singleton {
	
	public function render($path, $params) {
		$out = '';
		ob_start();
		// Include
		include_once __DIR__ . '/../AstelShared/View/' . $path . '.php';
		$out = ob_get_contents();
		ob_end_clean();
		
		return $out;
		// TODO return string
	}
}
