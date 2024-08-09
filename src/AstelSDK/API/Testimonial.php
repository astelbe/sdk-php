<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class Testimonial extends APIModel
{

  protected function getAll(array $params = [])
  {
    $query = $this->newQuery();
    $query->setUrl('v2_00/testimonial');
    $query->addGETParams($params);

    return $query->exec();
  }
}