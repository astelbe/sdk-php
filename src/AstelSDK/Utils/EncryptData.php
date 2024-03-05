<?php

namespace AstelSDK\Utils;

class EncryptData {

  protected static $encryptionKey;

  /**
   * @param string $encryptionKey
   */
  public static function setEncryptionKey($encryptionKey) {
    self::$encryptionKey = $encryptionKey;
  }

  /**
   * @param string
   */
  public static function getEncryptionKey() {
    return self::$encryptionKey;
  }

  public static function encrypt($data, $encryptionKey) {
    $ivLength = openssl_cipher_iv_length($cipher = 'AES-256-CBC');
    $iv = openssl_random_pseudo_bytes($ivLength);
    $encryptedData = openssl_encrypt($data, $cipher, $encryptionKey, 0, $iv);
    return base64_encode($encryptedData . '::' . $iv);
  }
}
?>