<?php

namespace AstelSDK\Utils;

class EncryptData {
  
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

  /**
   * Key used to encrypt the *public* AIM context (page_url, filters, session id...).
   * Derived from the shared secret (SSM aim.signing_key). Without secret (legacy setups), falls back
   * to the legacy hourly key so that nothing breaks before the secret is deployed.
   * The public context carries no authority: anything trusted goes through signContext().
   */
  public static function publicKey($secret) {
    if (empty($secret)) {
      return self::legacyKeys()[0];
    }
    return hash_hmac('sha256', 'aim-public-v1', $secret, true);
  }

  /**
   * Hourly keys of the old scheme, only to decrypt public params built by SDK versions
   * that have not been updated yet (current slot and neighbours, as the slot rolls every 30 min).
   */
  public static function legacyKeys() {
    $slot = round(time() / 3600) * 3600;
    return [
      self::generateDeterministicKey($slot),
      self::generateDeterministicKey($slot - 3600),
      self::generateDeterministicKey($slot + 3600),
    ];
  }

  /**
   * Signs a trusted context. Returns ['ctx' => base64url(json), 'sig' => hex hmac-sha256].
   * issued_at is added automatically.
   */
  public static function signContext(array $context, $secret) {
    $context['issued_at'] = time();
    $ctx = rtrim(strtr(base64_encode(json_encode($context)), '+/', '-_'), '=');
    return ['ctx' => $ctx, 'sig' => hash_hmac('sha256', $ctx, hash_hmac('sha256', 'aim-trusted-v1', $secret, true))];
  }

  /**
   * Verifies a signed context. Returns the decoded array, or null if the signature is wrong,
   * the payload malformed or older than $maxAge seconds.
   */
  public static function verifyContext($ctx, $sig, $secret, $maxAge = 7200) {
    if (empty($secret) || !is_string($ctx) || !is_string($sig) || $ctx === '' || $sig === '') {
      return null;
    }
    $expected = hash_hmac('sha256', $ctx, hash_hmac('sha256', 'aim-trusted-v1', $secret, true));
    if (!hash_equals($expected, $sig)) {
      return null;
    }
    $decoded = json_decode(base64_decode(strtr($ctx, '-_', '+/')), true);
    if (!is_array($decoded) || !isset($decoded['issued_at']) || !is_numeric($decoded['issued_at'])) {
      return null;
    }
    $age = time() - (int)$decoded['issued_at'];
    if ($age > $maxAge || $age < -300) {
      return null;
    }
    return $decoded;
  }

  public static function generateDeterministicKey($inputString) {
    return hash('sha256', $inputString, true);
  }
}
?>
