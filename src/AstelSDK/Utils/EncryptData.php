<?php

namespace AstelSDK\Utils;

class EncryptData {
  // public static function encrypt($data, $encryptionKey) {
  //   $ivLength = openssl_cipher_iv_length($cipher = 'AES-256-CBC');
  //   $iv = openssl_random_pseudo_bytes($ivLength);
  //   $encryptedData = openssl_encrypt($data, $cipher, $encryptionKey, OPENSSL_RAW_DATA, $iv);
  //   // Encode the encrypted data and IV together for safe transport
  //   return base64_encode($encryptedData . '::' . $iv);
  // }
  
  // protected static function decryptData($data, $encryptionKey) {
  //   $parts = explode('::', base64_decode($data), 2);
  //   if(count($parts) === 2) {
  //     list($encryptedData, $iv) = $parts;
  //     return openssl_decrypt($encryptedData, 'AES-256-CBC', $encryptionKey, OPENSSL_RAW_DATA, $iv);
  //   }
  // }
  public static function encrypt($data, $encryptionKey, $cipher = 'AES-256-CBC') {
    $ivLength = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivLength);
    $encryptedData = openssl_encrypt($data, $cipher, $encryptionKey, OPENSSL_RAW_DATA, $iv);
    $base64Encoded = base64_encode($encryptedData . '::' . $iv);
    return strtr($base64Encoded, '+/', '-_'); 
  }

  public static function decryptData($encryptedData, $encryptionKey, $cipher = 'AES-256-CBC') {
    $base64Encoded = strtr($encryptedData, '-_', '+/');
    $parts = explode('::', base64_decode($base64Encoded), 2);
    if(count($parts) === 2) {
      list($encryptedData, $iv) = $parts;
      return openssl_decrypt($encryptedData, $cipher, $encryptionKey, OPENSSL_RAW_DATA, $iv);
    }
  }

  public static function generateDeterministicKey($inputString) {
    return hash('sha256', $inputString, true);
  }
}
?>