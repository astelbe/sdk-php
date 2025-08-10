<?php

namespace AstelShared;

use AstelSDK\Utils\Singleton;
use CakeUtility\Hash;
use AstelSDK\AstelContext;

class GooglePlaceAutocomplete extends Singleton {


	public function getJsList() {
		$Context = AstelContext::getInstance();

		return [
			'https://maps.googleapis.com/maps/api/js?key=' . $Context->apiKeyGooglePlace . '&libraries=places',
      'https://files' . $Context->getEnv() . '.astel.be/DJs/google_place_autocomplete.js?v=' . $Context->getVersion(),
		];
	}

	public function getJsScripts() {
		$out = '';
		foreach ($this->getJsList() as $js) {
			$out .= '<script type="text/javascript" src="' . $js . '"></script>';
		}

		return $out;
	}

	
  /**
   * Get html for Google Place Autocomplete to lete user select address
   * @param array $data orderRequest data
   * @param string $id id to prefix fields like 'official_street1'
   * @return string html
   */
  public function generateGooglePlaceAutocomplete($data, $id = '', $options = []) {
    // Main autocomplete have no id
    // Other autocomplete have an id to prefix fields like 'official_street1'
    $id_ = $id ? $id . '_' : '';

    ob_start();
?>
    <div class="form-group d-block google-place-autocomplete">
      <label for="email" class="control-label col-md-0">
        <?= __d('OrderAstelBe', 'autocomplete_address_label', true) ?>
      </label>
      <div id="autocomplete_wrapper<?= $id ?>" class="mb-2">
      </div>

      <?php
      foreach (
        [
          [
            'label' => 'autocomplete-found-street',
            'varName' => 'street1',
            'value' => $data[$id_ . 'street1'],
          ],
          [
            'label' => 'autocomplete-found-street-number',
            'varName' => 'street_number',
            'value' => $data[$id_ . 'street_number'],
          ],
          [
            'label' => 'autocomplete-found-postal-code',
            'varName' => 'postal_code',
            'value' => $data[$id_ . 'postal_code'],
          ],
          [
            'label' => 'autocomplete-found-city',
            'varName' => 'city',
            'value' => $data[$id_ . 'city'],
          ],

        ] as $addressComponent
      ) { ?>
        <div class="d-flex align-items-baseline flex-column flex-sm-row my-1">
          <div>

            <span class="text-nowrap">
              <?= __d('OrderAstelBe', $addressComponent['label']) ?>
            </span>
            <span id="<?= $id_ . $addressComponent['varName'] ?>_status" class="pl-1">
              <?php if (!empty($addressComponent['value'])) { ?>
                <?= $addressComponent['value'] ?> <i class="fa fa-check" style="color:#28a745ad;"></i>
              <?php } else { ?>
                <i class="fa fa-close" style="color:#ce0000d1;"></i>
              <?php } ?>
            </span>
          </div>
          <?php
          // display a street number or postal code field if not found by google API to let user fill it manually
          foreach (['street_number', 'postal_code'] as $field) {
            if ($addressComponent['varName'] == $field) { ?>
              <input
                name="data[OrderRequest][<?= $id_ . $field ?>_override]"
                id="<?= $id_ . $field ?>_override"
                class="ml-2 validate-field required input-not-valid d-none form-control"
                autocomplete="unknown"
                data-validate="<?= $field == 'street_number' ? 'latinCharNum' : 'postalCode' ?>"
                maxlength="15"
                type="text"
                placeholder="<?= __d('OrderAstelBe', $field . ' override placeholder'); ?>" style="width: 250px;">
          <?php }
          } ?>
        </div> <!-- Validation status -->
      <?php } ?>
    </div>
<?php

    $html = ob_get_clean();

    // add hidden address fields if necessary
    if ($options['use_hidden_fields']) {
      $html .= $this->hiddenAddressInputFields();
    }
    return $html;
  }

  /**
   * Add hidden fields of address to be completed after the return of the google api response
   * Used for frontend. 
   * In Order form those fields are already in the template to handle customer data
   */
  private function hiddenAddressInputFields () {

    $html = '';

    foreach (['street1', 'street_number', 'postal_code', 'city'] as $input) {

      $html .= '<input 
        name="data[OrderRequest][' . $input . ']" 
        id="' . $input . '" 
        value="" 
        class="validate-field required form-control input-complete" 
        autocomplete="unknown" 
        data-validate="latinCharNum" 
        maxlength="255" 
        type="hidden"
        >
      ';
      
    }

    return $html;
  }
}
