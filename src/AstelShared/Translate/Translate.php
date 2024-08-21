<?php

namespace AstelShared\Translate;

use AstelSDK\Utils\Singleton;
use AstelSDK\AstelContext;

class Translate extends Singleton {

  public $language = 'FR';
  public $translations;

  public function __construct() {
    $this->language = AstelContext::getInstance()->getLanguage();
    $this->translations = require_once('translations.php');
  }

  /**
   * $translator = Translate::getInstance();
   * Translate::get('number_tv_channel', $placeholders);
   */
  public static function get($key, $placeholders = null) {
    $instance = self::getInstance(); // Get the current instance of the class

    // Check if the key exists in the translations array
    if (array_key_exists($key, $instance->translations)) {
      // Check if the language exists for the given key
      if (array_key_exists($instance->language, $instance->translations[$key])) {
        // Retrieve the translation
        $translation = $instance->translations[$key][$instance->language];

        // If there are variables to replace in the translation string
        if ($placeholders !== null) {
          // If $placeholders is not an array, make it an array
          if (!is_array($placeholders)) {
            $placeholders = [$placeholders];
          }
          // Replace %s with the variables in $placeholders
          $translation = vsprintf($translation, $placeholders);
        }

        return $translation;
      } else {
        // Language does not exist for the key
        return "Translation not available for the selected language.";
      }
    } else {
      // Key does not exist in the translations
      return "Translation key does not exist.";
    }
  }
}
