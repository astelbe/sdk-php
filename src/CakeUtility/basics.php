<?php

namespace CakeUtility;
/**
 * Basic CakePHP functionality.
 *
 * Core functions for including other source files, loading models and so forth.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       Cake
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Basic defines for timing functions.
 */
	define('SECOND', 1);
	define('MINUTE', 60);
	define('HOUR', 3600);
	define('DAY', 86400);
	define('WEEK', 604800);
	define('MONTH', 2592000);
	define('YEAR', 31536000);


if (!function_exists('debug')) {
/**
 * Prints out debug information about given variable.
 *
 * Only runs if debug level is greater than zero.
 *
 * @param mixed $var Variable to show debug information for.
 * @param bool $showHtml If set to true, the method prints the debug data in a browser-friendly way.
 * @param bool $showFrom If set to true, the method prints from where the function was called.
 * @return void
 * @link https://book.cakephp.org/2.0/en/development/debugging.html#basic-debugging
 * @link https://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#debug
 */
	function debug($var, $showHtml = null, $showFrom = true) {
		$file = '';
		$line = '';
		$lineInfo = '';
		if ($showFrom) {
			$trace = Debugger::trace(array('start' => 1, 'depth' => 2, 'format' => 'array'));
			$file = $trace[0]['file'];
			//$file = str_replace(array(CAKE_CORE_INCLUDE_PATH, ROOT), '', $trace[0]['file']);
			$line = $trace[0]['line'];
		}

		echo '<pre>';
		echo "<b>$file.$line.</b>\n";
		print_r($var);
		echo '</pre>';
	}

}

if (!function_exists('stackTrace')) {

/**
 * Outputs a stack trace based on the supplied options.
 *
 * ### Options
 *
 * - `depth` - The number of stack frames to return. Defaults to 999
 * - `args` - Should arguments for functions be shown? If true, the arguments for each method call
 *   will be displayed.
 * - `start` - The stack frame to start generating a trace from. Defaults to 1
 *
 * @param array $options Format for outputting stack trace
 * @return mixed Formatted stack trace
 * @see Debugger::trace()
 */
	function stackTrace(array $options = array()) {
		if (ENV != 'dev') {
			return;
		}

		$options += array('start' => 0);
		$options['start']++;
		echo Debugger::trace($options);
	}

}

if (!function_exists('h')) {

	/**
	 * Convenience method for htmlspecialchars.
	 *
	 * @param string|array|object $text Text to wrap through htmlspecialchars. Also works with arrays, and objects.
	 *    Arrays will be mapped and have all their elements escaped. Objects will be string cast if they
	 *    implement a `__toString` method. Otherwise the class name will be used.
	 * @param bool $double Encode existing html entities
	 * @param string $charset Character set to use when escaping. Defaults to config value in 'App.encoding' or 'UTF-8'
	 * @return string|array|object Wrapped text, Wrapped Array or Wrapped Object
	 * @link https://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#h
	 */
	function h($text, $double = true, $charset = null) {
		if (is_string($text)) {
			//optimize for strings
		} elseif (is_array($text)) {
			$texts = array();
			foreach ($text as $k => $t) {
				$texts[$k] = h($t, $double, $charset);
			}
			return $texts;
		} elseif (is_object($text)) {
			if (method_exists($text, '__toString')) {
				$text = (string)$text;
			} else {
				$text = '(object)' . get_class($text);
			}
		} elseif (is_bool($text)) {
			return $text;
		}

		static $defaultCharset = false;
		if ($defaultCharset === false) {
			$defaultCharset = 'UTF-8';
			if ($defaultCharset === null) {
				$defaultCharset = 'UTF-8';
			}
		}
		if (is_string($double)) {
			$charset = $double;
		}
		return htmlspecialchars($text, ENT_QUOTES, ($charset) ? $charset : $defaultCharset, $double);
	}

}
