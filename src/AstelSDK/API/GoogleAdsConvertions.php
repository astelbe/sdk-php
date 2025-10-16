<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class GoogleAdsConvertions extends APIModel {

  protected function getAll(array $params = []) {
    $query = $this->newQuery();
    $query->setUrl('v2_00/google_ads_conversions');
    $default_params = [
      //			'accessible_as_page_in_front' => 1,
    ];

    $params = Hash::merge($default_params, $params);

    $query->addGETParams($params);

    return $query->exec();
  }

  /**
   * Get ALL conversions without pagination limit
   * This method will fetch all records by making multiple API calls if needed
   */
  public function getAllWithoutLimit(array $params = []) {
    // First, try with a very high limit or no_limit parameter
    $params_no_limit = array_merge($params, ['no_limit' => 1, 'limit' => 999999]);

    try {
      $result = $this->find('all', $params_no_limit);

      // If we got more than 25 results, the no_limit worked
      if (is_array($result) && count($result) > 25) {
        return $result;
      }
    } catch (Exception $e) {
      // no_limit parameter might not be supported, fall back to pagination
    }

    // Fallback: Use pagination to get all records
    return $this->getAllWithPagination($params);
  }

  /**
   * Get all conversions using pagination (fallback method)
   */
  private function getAllWithPagination(array $params = []) {
    $allConversions = [];
    $offset = 0;
    $batchSize = 25; // API fixed limit

    do {
      $batchParams = array_merge($params, ['offset' => $offset]);
      $batch = $this->find('all', $batchParams);

      if (is_array($batch) && !empty($batch)) {
        // Handle HAL format if present
        if (isset($batch['_embedded']['items'])) {
          $conversions = $batch['_embedded']['items'];
        } else {
          $conversions = $batch;
        }

        $allConversions = array_merge($allConversions, $conversions);
        $offset += $batchSize;

        // Continue if we got a full batch
        $shouldContinue = (count($conversions) === $batchSize);
      } else {
        $shouldContinue = false;
      }
    } while ($shouldContinue);

    return $allConversions;
  }

  /**
   * Debug method to test different pagination parameters
   * Call this to understand how the API handles limits and pagination
   */
  public function debugPagination($days = 30) {
    echo "=== GOOGLE ADS CONVERSIONS - PAGINATION DEBUG ===\n";
    echo "Testing various pagination parameters...\n\n";

    $testParams = [
      ['days' => $days],
      ['days' => $days, 'limit' => 100],
      ['days' => $days, 'limit' => 500],
      ['days' => $days, 'limit' => 1000],
      ['days' => $days, 'limit' => 5000],
      ['days' => $days, 'limit' => 10000],
      ['days' => $days, 'per_page' => 1000],
      ['days' => $days, 'page_size' => 1000],
      ['days' => $days, 'size' => 1000],
      ['days' => $days, 'max_results' => 1000],
      ['days' => $days, 'offset' => 0, 'limit' => 1000],
      ['days' => $days, 'page' => 1, 'per_page' => 1000],
    ];

    foreach ($testParams as $index => $params) {
      echo "Test " . ($index + 1) . ": " . json_encode($params) . "\n";

      try {
        $result = $this->find('all', $params);

        if (is_array($result)) {
          echo "  → Raw result count: " . count($result) . "\n";

          // Check for HAL format
          if (isset($result['_embedded']['items'])) {
            echo "  → HAL format detected\n";
            echo "  → Items count: " . count($result['_embedded']['items']) . "\n";

            if (isset($result['_embedded']['total'])) {
              echo "  → Total available: " . $result['_embedded']['total'] . "\n";
            }
            if (isset($result['_embedded']['count'])) {
              echo "  → Count: " . $result['_embedded']['count'] . "\n";
            }
            if (isset($result['_links'])) {
              echo "  → Links available: " . implode(', ', array_keys($result['_links'])) . "\n";
            }
          } else {
            // Direct array format
            echo "  → Direct array format\n";
            if (!empty($result)) {
              echo "  → First item keys: " . implode(', ', array_keys($result[0])) . "\n";
            }
          }
        } else {
          echo "  → Result type: " . gettype($result) . "\n";
        }
      } catch (Exception $e) {
        echo "  → ERROR: " . $e->getMessage() . "\n";
      }

      echo "  ---\n";
    }

    echo "\n=== API QUERY DEBUG ===\n";
    try {
      // Test direct query access
      $query = $this->newQuery();
      $query->setUrl('v2_00/google_ads_conversions');
      $query->addGETParams(['days' => $days, 'limit' => 1000]);

      echo "Query URL: " . $query->getFullUrl() . "\n";
      echo "Query params: " . json_encode($query->getParams()) . "\n";
    } catch (Exception $e) {
      echo "Query debug error: " . $e->getMessage() . "\n";
    }

    echo "\n=== DEBUG COMPLETE ===\n";
  }
}
