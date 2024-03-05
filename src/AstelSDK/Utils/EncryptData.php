<?php

namespace AstelSDK\Utils;

class EncryptData {
  public static function encrypt($data, $encryptionKey) {
    $ivLength = openssl_cipher_iv_length($cipher = 'AES-256-CBC');
    $iv = openssl_random_pseudo_bytes($ivLength);
    // Ensure encryption key is in the correct format
    $encryptionKeyBin = hex2bin($encryptionKey);
    $encryptedData = openssl_encrypt($data, $cipher, $encryptionKeyBin, OPENSSL_RAW_DATA, $iv);
    // Encode the combined encrypted data and IV for safe transport
    return base64_encode($encryptedData . '::' . $iv);
  }
  
  public static function generateDeterministicKey($inputString) {
    $hash = hash('sha256', $inputString);
    return hex2bin($hash);
  }
}
?>