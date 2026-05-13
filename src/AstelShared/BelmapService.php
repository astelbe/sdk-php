<?php



namespace AstelShared;

use AstelSDK\Utils\Singleton;
use AstelShared\Translate\Translate;


/**
 * Service to manage requests to the Belmap API
 * Manages address autocomplete and retrieval of entity details
 */
class BelmapService extends Singleton  {

  /**
   * Base URL for the Belmap API
   * @var string
   */
  private $baseUrl = 'https://belmapapi.gim.be/belmap-api/1_0_0/rest';

  /**
   * Base URL for the Belmap Query API
   * @var string
   */
  private $queryApiUrl = 'https://belmapapi.gim.be/api/query/address';

  /**
   * Address search with autocomplete
   * @param array $request Contains the 'address' key with the address to search
   * @param string $token Token for the Belmap API
   * @return array ['code' => int, 'response' => array or 'message' => string]
   */
  public function getBelmapAutocompleteAddress($request, $token) {
    try {
      if (!isset($request['address']) || empty($request['address'])) {
        return [
          'code' => 400,
          'message' => 'Address parameter is required'
        ];
      }

      if (empty($token)) {
        return [
          'code' => 400,
          'message' => 'Token parameter is required'
        ];
      }

      $url = $this->baseUrl . '/autocomplete?q=';
      $url .= urlencode($request['address']);
      $url .= '&countryCode=BEL&geoEntityType=Address&responseType=SUGGESTION&minSimilarity=0.5&limit=5&token=';
      $url .= $token;

      $response = $this->makeRequest($url);

      if (isset($response['error'])) {
        return $response;
      }

      return [
        'code' => 200,
        'response' => $response
      ];
    } catch (Exception $e) {
      return [
        'code' => $e->getCode(),
        'message' => $e->getMessage()
      ];
    }
  }

  /**
   * Retrieves the details of a Belmap address with subaddresses (box numbers)
   * @param array $request Contains the 'belmapId' key with the Belmap identifier
   * @param string $token Token for the Belmap API
   * @return array ['code' => int, 'response' => ['entity' => array, 'subaddresses' => array] or 'message' => string]
   */
  public function getBelmapAddressDetails($request, $token) {
    try {
      if (!isset($request['belmapId']) || empty($request['belmapId'])) {
        return [
          'code' => 400,
          'message' => 'BelmapId parameter is required'
        ];
      }

      if (empty($token)) {
        return [
          'code' => 400,
          'message' => 'Token parameter is required'
        ];
      }

      $belmapId = $request['belmapId'];
      $url = $this->baseUrl . '/entity/' . urlencode($belmapId) . '?countryCode=BEL&token=' . $token;

      $response = $this->makeRequest($url);

      if (isset($response['error'])) {
        return $response;
      }

      // Fetch subaddresses (box numbers) for this address
      $subaddresses = [];
      $subaddressesResponse = $this->getBelmapSubaddresses($belmapId, $token);
      if ($subaddressesResponse['code'] === 200) {
        $subaddresses = $subaddressesResponse['response'];
      }
  
      return [
        'code' => 200,
        'response' => [
          'entity' => $response,
          'subaddresses' => $subaddresses
        ]
      ];
    } catch (Exception $e) {
      return [
        'code' => $e->getCode(),
        'message' => $e->getMessage()
      ];
    }
  }

  /**
   * Retrieves all active subaddresses (box numbers) for a given address ID
   * @param string $belmapId The Belmap address identifier (must be a base address ID)
   * @param string $token Token for the Belmap API
   * @return array ['code' => int, 'response' => array of subaddresses or 'message' => string]
   */
  public function getBelmapSubaddresses($belmapId, $token) {
    try {
      if (empty($belmapId) || empty($token)) {
        return [
          'code' => 400,
          'message' => 'BelmapId and Token parameters are required'
        ];
      }

      // Query filter for active subaddresses
      $filter = [
        'endLifeSpan' => [
          '$exists' => false
        ],
        'subAddressOf' => (int)$belmapId
      ];

      $url = $this->queryApiUrl . '?countryCode=BEL&requestedFields=stableId&requestedFields=boxNumber&token=' . $token;

      $response = $this->makePostRequest($url, $filter);

      if (isset($response['error'])) {
        return $response;
      }

      return [
        'code' => 200,
        'response' => $response
      ];
    } catch (Exception $e) {
      return [
        'code' => $e->getCode(),
        'message' => $e->getMessage()
      ];
    }
  }

  /**
   * Performs an HTTP GET request to the Belmap API
   * @param string $url The complete request URL
   * @return array The JSON decoded data or an error array
   */
  private function makeRequest($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HEADER => false,
      CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'Content-Type: application/json'
      ]
    ]);

    $response = curl_exec($ch);
    $errorCode = curl_errno($ch);
    $errorMsg = curl_error($ch);

    curl_close($ch);

    if ($errorCode) {
      return [
        'error' => true,
        'code' => $errorCode,
        'message' => $errorMsg
      ];
    }

    return json_decode($response, true);
  }

  /**
   * Performs an HTTP POST request to the Belmap API
   * @param string $url The complete request URL
   * @param array $data The POST data (will be JSON encoded)
   * @return array The JSON decoded data or an error array
   */
  private function makePostRequest($url, $data = []) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HEADER => false,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => [
        'Accept: */*',
        'Content-Type: application/json'
      ]
    ]);

    $response = curl_exec($ch);
    $errorCode = curl_errno($ch);
    $errorMsg = curl_error($ch);

    curl_close($ch);

    if ($errorCode) {
      return [
        'error' => true,
        'code' => $errorCode,
        'message' => $errorMsg
      ];
    }

    return json_decode($response, true);
  }

  /**
   * Generates HTML for the Belmap address autocomplete input
   * @param array $options Configuration options
   *   - 'id': Custom ID (otherwise generated randomly)
   *   - 'label': Input label
   *   - 'placeholder': Input placeholder
   *   - 'input_value': Initial input value
   *   - 'input_name': Input name (default: 'belmapAddressInput')
   * @return string HTML of the Belmap autocomplete input
   */
  public function getBelmapAddressInput($options = []) {
    // Generate a random ID if not provided
    $id = isset($options['id']) ? $options['id'] : $this->generateRandomId();

    $label = isset($options['label']) ? $options['label'] :  Translate::get('autocomplete_address_label');
    $placeholder = isset($options['placeholder']) ? $options['placeholder'] : '';
    $inputValue = isset($options['input_value']) ? $options['input_value'] : '';
    $inputName = isset($options['input_name']) ? $options['input_name'] : 'belmapAddressInput';

    $html = '
    <div class="row">
        <div class="col-md-12">
        <label for="' . $id . '">' . $label . '</label>
        </div>
        <div class="col-md-12 ">
          <div class="search-container" style="position: relative;">
            <input 
              type="text" 
              placeholder="' . $placeholder . '" 
              name="' . $inputName . '" 
              id="' . $id . '" 
              class="form-control col col-lg-8 pac-target-input belmapAddressInput" 
              autocomplete="off"
              value="' . htmlspecialchars($inputValue) . '"
            >
            <ul id="resultBelmat_' . $id . '" class="resultBelmat suggestions" role="listbox" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-top: none; list-style: none; margin: 0; padding: 0; max-height: 300px; overflow-y: auto; z-index: 1000;">
              <li role="option" class="" style="display: none;"></li>
            </ul>
            <!-- Debug Belmap entity response -->
            <div id="belmap-entity-debug_' . $id . '" style="margin-top:10px;"></div>
          </div>
        </div>
      </div>';

    return $html;
  }

  /**
   * Generates HTML to display separated address details (visual validation)
   * HTML structure for individual fields that will be filled by JavaScript
   * Block hidden by default, visible only when an address is selected
   * @param array $options Configuration options
   *   - 'id': ID of the associated belmap input (default: 'belmapAddressInput')
   * @return string HTML with separated detail fields
   */
  public function getBelmapAddressDetailsDisplay($options = []) {
    $id = isset($options['id']) ? $options['id'] : 'belmapAddressInput';

    $addressComponents = [
      [
        'label' => 'autocomplete-found-street',
        'varName' => 'street1',
      ],
      [
        'label' => 'autocomplete-found-street-number',
        'varName' => 'street_number',
      ],
      // [
      //   'label' => 'autocomplete-found-box',
      //   'varName' => 'box',
      // ],
      [
        'label' => 'autocomplete-found-postal-code',
        'varName' => 'postal_code',
      ],
      [
        'label' => 'autocomplete-found-city',
        'varName' => 'city',
      ],
    ];

    $html = '<div id="belmap-address-details-' . htmlspecialchars($id) . '" class="belmap-address-details mb-4">';

    foreach ($addressComponents as $component) {
      $varName = $component['varName'];
      $label = Translate::get($component['label']);
      
      $html .= '
      <div class="d-flex align-items-baseline flex-column flex-sm-row">
        <div>
          <span class="text-nowrap pr-2" style="font-weight: 500;">
            ' . htmlspecialchars($label) . '
          </span>
        </div>
        <div style="flex: 1;">
          <span id="belmap_' . htmlspecialchars($id) . '_' . $varName . '_status" style="display: inline-block; ">
            <i class="fa fa-close" style="color:#ce0000d1;"></i>
          </span>
        </div>
      </div>';
    }

    $html .= '</div>';

    return $html;
  }

  /**
   * Generates a random ID
   * @return string Random ID
   */
  private function generateRandomId() {
    return 'belmap_' . substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(7 / strlen($x)))), 1, 7);
  }
}
