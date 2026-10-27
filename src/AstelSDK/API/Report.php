<?php

namespace AstelSDK\API;

/**
 * Reports of the partner of the token (aggregated with its child partners).
 * Same data as the CRM charts "Order Lines by Status color" and "# OR Processed".
 */
class Report extends APIModel {

  protected $customCacheTTL = 300;

  /**
   * Number of order lines per day and per status color, with the same period Y-1.
   *
   * @param array $params
   *   - date_from, date_to: Y-m-d, inclusive (default: last 30 days, max 731 days)
   *   - date_type: 'order_date' (default) or 'status_log' (last status change)
   *   - status_id, language, is_pro, brand_id: comma separated lists (is_pro: 0 private, 1 pro)
   *
   * @return array date_type, date_from, date_to, trend_periods, series, series_y_minus_1
   *   with series = [['color' => 'GREEN', 'label' => 'PROCESSED', 'color_code' => '#49bf67', 'data' => [[timestamp, count], ...]], ...]
   * @throws \AstelSDK\Exception\ValidationErrorException
   * @throws \AstelSDK\Exception\DataException
   */
  public function getOrderLinesByStatusColor(array $params = []) {
    return $this->getReport('v2_00/report/order_lines_by_status_color', $params);
  }

  /**
   * Number of processed orders per month, for the current year and the 3 previous ones.
   *
   * @param array $params
   *   - brand_id: comma separated list (default: all the brands visible in the dashboards)
   *
   * @return array brand_id, series = [['year' => 2026, 'data' => [[month, count], ...]], ...]
   * @throws \AstelSDK\Exception\ValidationErrorException
   * @throws \AstelSDK\Exception\DataException
   */
  public function getOrderProcessed(array $params = []) {
    return $this->getReport('v2_00/report/order_processed', $params);
  }

  private function getReport($url, array $params) {
    if (!empty($params['nocache'])) {
      $this->disableCache = true;
    }
    $query = $this->newQuery();
    $query->setUrl($url);
    $query->addGETParams($params);
    $response = $query->exec();
    $this->disableCache = false;

    $this->handlesResponseThrows($response);

    return $this->returnResponse($response, self::FIND_TYPE_FIRST);
  }
}
