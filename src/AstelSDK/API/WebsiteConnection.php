<?php

namespace AstelSDK\API;

use AstelSDK\Model;
use AstelSDK\Utils\Numbers;
use CakeUtility\Hash;

class WebsiteConnection extends Model implements IApiConsumer {
	
	protected function getFirst(array $params = []) {
		$default_params = [
			'unique_visitor_key' => $this->context->getUniqueVisitorKey(),
			'language' => $this->context->getLanguage(),
		];
		$params = Hash::merge($default_params, $params);
		$query = $this->newQuery();
		$query->addGETParams($params);
		$query->setUrl('v2_00/website_connection/');
		
		return $query->exec();
	}
	
	/**
	 * @param $websiteConnect
	 * @param $operators : needed to add brand info to products without a new call to api
	 *
	 * @return (array) $cart
	 */
	public function getCart($websiteConnect, $operators) {
		if (is_object($websiteConnect)) {
			$websiteConnect = $websiteConnect->current();
		}
		$cart = Hash::get($websiteConnect, 'session.order_form.cart');
		// Products
		if (is_array($cart['products'])) {
			foreach ($cart['products'] as $k => $product) {
				$brand_color = Hash::extract($operators, '{n}[id=' . $product['brand_id'] . '].fact_sheet.color_code');
				$cart['products'][$k]['brand_color'] = $brand_color[0];
				if (empty($product['banner'])) {
					$banner = Hash::extract($operators, '{n}[id=' . $product['brand_id'] . '].fact_sheet.logo.small');
					$cart['products'][$k]['banner'] = $banner[0];
				}
				if ($product['discounted_price'] != 0 && $product['price'] !== $product['discounted_price']) {
					$price = '<del class="text-lighter pr-1">' . $product['price'] . '</del> ' . Numbers::priceDisplayLocale($product['discounted_price']);
				} else {
					$price = Numbers::priceDisplayLocale($product['price']);
				}
				$cart['products'][$k]['display_price'] = $price . ' ' . translate('per_month');
				$cart['products'][$k]['cashback_amount'] = ($product['cashback_amount'] == 0 ? $product['cashback_amount'] : Numbers::priceDisplayLocale($product['cashback_amount']));
			}
		}
		// Total price
		if ($cart['total']['discounted_price'] != 0 && $cart['total']['price'] !== $cart['total']['discounted_price']) {
			$totalPrice = '<del class="text-lighter pr-1">' . $cart['total']['price'] . '</del> ' . Numbers::priceDisplayLocale($cart['total']['discounted_price']);
		} else {
			$totalPrice = Numbers::priceDisplayLocale($cart['total']['price']);
		}
		$cart['total']['display_price'] = $totalPrice . ' ' . translate('per_month');
		// Total cashback
		$cart['total']['display_cashback_amount'] = ($cart['total']['cashback_amount'] == 0 ? $cart['total']['cashback_amount'] : Numbers::priceDisplayLocale($cart['total']['cashback_amount']));
		$cart['total']['cashback_amount'] = $cart['total']['cashback_amount'];
		$cart['order_link'] = Hash::get($websiteConnect, 'session.order_form.referer_page');
		$cart['order_request_id'] = Hash::get($websiteConnect, 'session.order_form.order_request_id');
		
		return $cart;
	}
	
	public function clearCart() {
		$params = [
			'unique_visitor_key' => $this->context->getUniqueVisitorKey(),
		];
		$query = $this->newQuery();
		$query->setUrl('v2_00/website_connection/cart');
		$query->addGETParams($params);
		$query->setHTTPMethod($query::HTTP_DELETE);
		
		return $query->exec();
	}
}