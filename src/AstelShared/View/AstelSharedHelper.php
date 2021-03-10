<?php

use CakeUtility\Hash;
use AstelSDK\Utils\VatCalculation;

class GeneralHelper {


    /**
     * @param null $playDescriptionPath
     * @param null $product
     *
     * Get product details for view
     * !! Set to render tab mobile / internet translated keys !!
     *
     * @return mixed|null
     */
    static function getProductInfo($playDescriptionPath = null, $product = null, $responsive = null) {

        $description = Hash::get($product, $playDescriptionPath, null);
        if (!$playDescriptionPath || !$product || !$description) {
            return null;
        }

        switch ($playDescriptionPath) {

            // Mobile
            case 'play_description.mobile.included_minutes_calls' :
                if ($description == 'UNLIMITED') {
                    return d__('product', 'tab_mobile_unlimited_call');
                } else {
                    return d__('product', 'tab_mobile_minutes' . $responsive, $description);
                };
                break;
            case 'play_description.mobile.included_data_volume' :
                if ($description == 'UNLIMITED') {
                    return d__('product', 'tab_mobile_unlimited_internet');
                } else {
                    return d__('product', 'tab_mobile_gb_data' . $responsive, $description / 1000);
                }
                break;
            case 'play_description.mobile.included_sms' :
                if ($description == 'UNLIMITED') {
                    return d__('product', 'tab_mobile_unlimited_sms');
                } else {
                    return d__('product', 'tab_mobile_sms', $description);
                }
                break;
            // Internet
            case 'play_description.internet.bandwidth_download' :
                return d__('product', 'tab_internet_mbps', $description);
                break;
            case 'play_description.internet.bandwidth_upload' :
                return d__('product', 'tab_internet_mbps', $description);
                break;
            case 'play_description.internet.bandwidth_volume' :
                if ($description == 'UNLIMITED') {
                    return d__('product', 'tab_unlimited');
                } else {
                    return d__('product', 'tab_mobile_gb_data', $description);
                }
            default:
                return $description;
        }
    }

    static function getTranslation($key, $version, $params = []) {
        switch ($version) {
            case 'cake' :
                return __d()
        }

    }

}