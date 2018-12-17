<?php

namespace AstelSDK\API;

interface IApiConsumer {
	public function find($type, array $params = []);
}