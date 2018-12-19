<?php

namespace AstelSDK\API;

use AstelSDK\QueryManager;
use AstelSDK\Utils\Numbers;
use CakeUtility\Hash;

class WebsiteConnection extends QueryManager implements IApiConsumer {
	public function find($type, array $params = []) {
		$cacheKey = md5(print_r($params, true));
		if (isset($this->cacheResults[$cacheKey])) {
			return $this->cacheResults[$cacheKey];
		}
		$result = false;
		if ($type === 'first' || $type === 'all') {
			$result = $this->getFirst($params);
		}
		$this->cacheResults[$cacheKey] = $result;
		
		return $result;
	}
	
	protected function getFirst(array $params = []) {
		global $sLang;
		$uniqueVisitorKey = md5($this->getUserIP() . $_SERVER['HTTP_USER_AGENT']);
		$default_params = [
			'conditions' => [
				'unique_visitor_key' => $uniqueVisitorKey,
				'language' => $sLang,
			],
		];
		$params = Hash::merge($default_params, $params);
		$this->init();
		$url = 'v2_00/website_connection/';
		$url = $this->addUrlParams($url, $params, true);
		$this->setUrl($url);
		
		return $this->exec(self::RETURN_MULTIPLE_ELEMENTS);
	}
	
	/**
	 * @param $websiteConnect
	 * @param $operators : needed to add brand info to products without a new call to api
	 *
	 * @return (array) $cart
	 */
	public function getCart($websiteConnect, $operators) {
		$cart = Hash::get($websiteConnect, 'session.order_form.cart');
		// Products
		if (is_array($cart['products'])) {
			foreach ($cart['products'] as $k => $product) {
				$brand_color = Hash::extract($operators, '{n}[id=' . $product['brand_id'] . '].fact_sheet.color_code');
				$cart['products'][$k]['brand_color'] = $brand_color[0];
				$cart['products'][$k]['banner'] = [];
				if (empty($product['banner'][SCOPE_LANG])) {
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
		$cart['total']['cashback_amount'] = ($cart['total']['cashback_amount'] == 0 ? $cart['total']['cashback_amount'] : Numbers::priceDisplayLocale($cart['total']['cashback_amount']));
		$cart['order_link'] = Hash::get($websiteConnect, 'session.order_form.referer_page');
		$cart['order_request_id'] = Hash::get($websiteConnect, 'session.order_form.order_request_id');
		
		return $cart;
	}
	
	public function clearCart() {
		// TODO empty order session
		return true;
	}
}