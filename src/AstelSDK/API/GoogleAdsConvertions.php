<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

/**
 * Google Ads Conversions API
 * Uses Redis cache maintained by Queue Job backend_google_ads_conversions
 * Pattern: Dashboard market_shares
 * 
 * Cache is automatically maintained hourly by Queue Job and stored in Redis.
 * Available cache periods: ALL, 30, 60, 90, 365 days
 */
class GoogleAdsConvertions extends APIModel {

  /**
   * Get Google Ads conversions from cache (FAST - milliseconds)
   * 
   * The backend API automatically uses Redis cache maintained by Queue Job.
   * Cache is regenerated every hour with delta updates (2h lookback).
   * 
   * @param array $params
   *   - days: Number of days to retrieve (default: 30, use 0 for ALL data)
   *   - use_cache: 'true' (default) or 'false' to bypass cache and query database
   * 
   * @return array API response with conversions data
   * 
   * @example
   *   // Get last 30 days from cache (FAST)
   *   $conversions = $GoogleAds->find('all', ['days' => 30]);
   * 
   *   // Get ALL data from cache (FAST)
   *   $conversions = $GoogleAds->find('all', ['days' => 0]);
   * 
   *   // Bypass cache and query database (SLOW)
   *   $conversions = $GoogleAds->find('all', ['days' => 30, 'use_cache' => 'false']);
   */
  protected function getAll(array $params = []) {
    $query = $this->newQuery();
    $query->setUrl('v2_00/google_ads_conversions');

    $default_params = [
      'use_cache' => 'true', // Use Redis cache by default (maintained by Queue Job)
      'days' => 30,          // Default: last 30 days
    ];

    $params = Hash::merge($default_params, $params);

    $query->addGETParams($params);

    return $query->exec();
  }
}
