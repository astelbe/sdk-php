<?php

namespace AstelSDK\Model;

class ProductCompare extends SDKModel {
	
	const ORDER_PRICE = 'price';
	const ORDER_QUALITY = 'quality';
	const ORDER_QUALITY_PRICE = 'qualityprice';
	const ORDER_DELAY = 'delay';
	
	protected $associated_instance_name = '\AstelSDK\API\ProductCompare';
	
}