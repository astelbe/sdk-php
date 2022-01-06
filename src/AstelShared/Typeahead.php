<?php
namespace AstelShared;

use AstelSDK\Utils\Singleton;
use CakeUtility\Hash;
use AstelSDK\AstelContext;

class Typeahead extends Singleton {

	/**
	 * Typeahead have an hidden_input to pass value in form. to avoid Chrome autocomplete, input name is randomized,
	 * then its value is taken to fill the hidden_input
	 * For postal codes, input value is code + name, and hidden is only zip code. Backend only need zip code
	 * Js script : public_html/app/Plugin/FilesAstelBe/View/DJs/astel_content_injector.ctp
	 * I.e for postal code, "1000 - Bruxelles" is displayed but "1000" is passed
	 *
	 * Css is in astel_standalone.css
	 *
	 */

	// Attributes as options
	public $typeahead_id = ''; // Used if multiple typeahead in the same page
	public $label = null;
	public $placeholder = '';
	public $input_value = '';
	public $disabled = false; // Used in BC when address is already filled
	public $show_clear_button = false;
	public $hidden_input_name = 'hidden_input_name'; //hidden_input_name (input used when submitting the form)
	public $hidden_input_value = ''; //hidden_input_value (input used when submitting the form)

	public function assignAttributes ($options = []) {
		$attributes_as_options = [
			'typeahead_id', 'label','placeholder', 'input_value', 'disabled', 'show_clear_button', 'hidden_input_name', 'hidden_input_value',
		];
		foreach ($attributes_as_options as $option) {
			if (isset($options[$option])) {
				$this->$option = Hash::get($options, $option);
			}
		}
	}

	public function getJsList () {
		$Context = AstelContext::getInstance();
		return [
			'https://files' . $Context->getEnv() . '.astel.be/DJs/astelPostalCodes/postal_codes_' . $Context->getLanguage() . '.js?v=' . $Context->getVersionData(),
			'https://files' . $Context->getEnv() . '.astel.be/DJs/typeahead.js?v=' . $Context->getVersion(),
		];
	}



	public function getTypeaheadScripts () {
		$out = '';
		foreach ($this->getJsList() as $js) {
			$out .= '<script type="text/javascript" src="' . $js . '"></script>';
		}

		return $out;
	}


	/**
	 * @param array $options
	 * - 'full_postal_code' array - Postal code from db, get by postal_code_id from session
	 * - other options : cf class attributes
	 *
	 * @return string - typeahead's html
	 */
	// TODO Params non de callback apiGateway
	public function getPostalCodeTypeahead ($options = []) {
		$Context = AstelContext::getInstance();
		$this->assignAttributes($options);
		$full_postal_code = Hash::get($options,'full_postal_code', null);
		if (!empty($full_postal_code)) {
			// BC, OrderRequest postal codes are already processed with the translated name in 'city_name'
			if (Hash::get($full_postal_code, 'city_name', null)) {
				$name = Hash::get($full_postal_code, 'city_name', null);
			} else {
				$name = Hash::get($full_postal_code, 'name.' . $Context->getLanguage());
			}
			$this->input_value = Hash::get($full_postal_code, 'postal_code') . ' - ' . $name;
			$this->hidden_input_value = Hash::get($full_postal_code, 'id') ;
		}
		return $this->getHtml($options);
	}

	public function getHtml() {
		$random_string = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(5/strlen($x)) )),1,7);
		$label = $this->label ? '<label for="' . $random_string . '">' . $this->label . '</label>' : '';

		$html = '
			<div class="mb-2">' . $label . '</div>
			<div class="d-flex mb-2">
				<div class="flex-fill">
					<div class="typeahead border rounded position-relative">
						<input
							type="text"
							aria-autocomplete="list"
							name="' . $random_string . '"
							class="typeahead__input form-control required border-0' . ($this->input_value == '' ? ' input_not_complete' : ' input_complete') . '"
							id="typeahead__input' . $this->typeahead_id . '" value="' . $this->input_value . '"
							placeholder="' . __d('CoreAstelBe', 'Postal code and city placeholder') . '"
							' . ($this->disabled ? ' disabled="disabled" ' : '') .'
							autocomplete="unknown"
						/>
						<ul class="typeahead__results" id="typeahead__results' . $this->typeahead_id . '"></ul>
					</div>
				</div>';
		$html .= $this->show_clear_button ?
			'<div class="pl-2 pt-2" id="typeahead_clear_btn">
				<i class="fa fa-times-circle text-muted font-s-12" aria-hidden="true"></i>
			</div>' : '';
		$html .= '</div>';

		// Hidden input
		$html .= '<input type="hidden" id="typeahead__hidden_input' . $this->typeahead_id . '" name="' . $this->hidden_input_name . '" value="' . $this->hidden_input_value . '">';

		return $html;
	}

}
?>