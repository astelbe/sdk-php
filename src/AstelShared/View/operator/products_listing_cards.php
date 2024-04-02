<?php

use CakeUtility\Hash;
// debug($params);

/*
  From public_html_v1/www/public_html/app/view/helper/ProductsListingCards.php
  From comp
	This template received $params :	array of results with products and pricings
	$params = [
        'title' => 'Title of the result',
	    'id' => 'list_id', // for toggle details
        'options' => [
            'display_operator_in_product_name' => true/false, // default true, to noyt display logo in operator page
        'results' => [
            0 =>[
                'products' => [
                    [
                        'brand_name' => 'Proximus',
                        'short_name' => 'Tuttimus',
                        '...' => ...,
                    ],
                ],
                'result_summary' => [
                    'order_url' => 'https://www.proximus.be',
                    'displayed_price' => 99,
                    'products_total_savings' => 99,
                    'setup' => '...',
                    'max_activation_time' => '...',
                    'phone_plug' => '...',
                    'total_cashback' => '...',
                    '...' => ...,
                ],
            ],
            ...
        ],
    ];
*/

/*
$params = [

  'results' = [

    0 => [
      'products' = [

        'name' => ... ,
        'name' => ... ,
        'name' => ... ,
        'play_types' => [
          ... ,
          ... ,
          ... ,
        ]
      ]
    ]

    1 => [
      'products' = [

        0 => [
          'name' => ... ,
          'name' => ... ,
          'name' => ... ,
          'play_types' => [
            ... ,
            ... ,
            ... ,
          ]
        ],

        1 => [
          'name' => ... ,
          'name' => ... ,
          'name' => ... ,
          'play_types' => [
            ... ,
            ... ,
            ... ,
          ]
        ],
      ]
      
    ]
  ]
]
*/

?>

<div class="container px-0 toggleProductListingDetails__container" id="toggleProductListingDetails__container_<?= $params['id'] ?>">
  <div class="d-md-flex justify-content-between align-items-center" >
    <h2 class="mt-2 pl-2">
      <?= $params['title']; ?>
    </h2>
    <div class="btn btn-outline-secondary text-uppercase cursor-pointer d-flex justify-content-center text-nowrap toggleProductListingDetails__button" id="toggle-product-listing-button-<?= $params['id'] ?>" onclick="toggleProductListingCards('<?= $params['id'] ?>')">
      <div class="details-hidden">
        <?= self::getTranslation(['cake' => 'CompareAstelBe', 'front' => 'product'], 'switch_resume', $this->version) ?>&nbsp;<i class="fa fa-chevron-up ml-2" aria-hidden="true"></i>
      </div>
      <div class="details-visible">
        <?= self::getTranslation(['cake' => 'CompareAstelBe', 'front' => 'product'], 'switch_details', $this->version) ?>&nbsp;<i class="fa fa-chevron-down ml-2" aria-hidden="true"></i>
      </div>
    </div>
  </div>


  <div class="row mt-4">
    <?php foreach ($params['results'] as $key => $result) {
      $cashback = ($result['result_summary']['total_cashback'] != '' && $result['result_summary']['total_cashback'] !== 0) ? $result['result_summary']['total_cashback'] : false;
    ?>
      <div class="col-12 col-xl-3 col-lg-4 col-md-6 mb-5 mb-5 mt-4 product-card px-2">
       
        <div class="px-3 pt-3 pb-2 rounded-15 d-flex h-100 flex-column justify-content-between align-item-end" style="box-shadow: 0 2px 30px 0 rgba(0, 0, 0, 0.1);">
          <?php if ($cashback) { ?>

            <div class="py-2 px-3 mb-3 text-center cursor-pointer rounded-xl plugin-hidden-optional-element cashback-amount modalClick " data-toggle="modal" data-target="#pluginModalCashback" style="color:#fff; background-color: #E5176B; margin:0 auto;">
              <?= $cashback ?> <i class="fa fa-info pl-1" style="font-size:1rem"></i>
            </div>
          <?php } ?>
         
          <?php
          $cpt = 1; // To display "+"
          foreach ($result['products'] as $key => $item) {
            if ($cpt > 1) { ?>
              <svg class="w-100 mb-3" width="260" height="30" viewBox="0 0 260 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <line y1="15.5" x2="110" y2="15.5" stroke="#D8D8D8" />
                <line x1="150" y1="15.5" x2="260" y2="15.5" stroke="#D8D8D8" />
                <rect x="126" width="8" height="30" fill="#6CC1F0" />
                <rect x="115" y="11" width="30" height="8" fill="#6CC1F0" />
              </svg>
            <?php } ?>

            <?php
            // Display brand name only if 1st product , and also 2dn result if multi brand result
            if (($cpt == 1 || ($cpt == 2 && $params['id'] == 'view_multi_brand')) && $params['options']['display_operator_in_product_name'] !== false) { ?>
                <div class="mb-3 text-center" style="height: 36px">
                  <img src="<?= $item['brand_logo'] ?>" alt="<?= $item['brand_name'] ?>" style="max-height:28px;">
                </div>
            <?php } ?>
            <?php if ($item['product_sheet_url'] != '') { ?>
              <a class="gtm-product-detail-link" href="<?= $item['product_sheet_url'] ?>" title="<?= $item['short_name'] ?>" target="_blank" data-name="<?= $item['short_name']  ?>" data-brand="<?= $item['brand_name'] ?>">
              <?php } ?>
              <h3 class="px-1 d-flex justify-content-center text-center font-weight-bold" <?= ($cpt == 1 ? 'style="min-height: 74px; font-size: 1.2rem;"' : '') ?>>
                <span class="w-100"><?= $item['short_name']; ?></span>
                <span><?= self::getDisplayedProductCount($item) ?></span>
              </h3>
              <?php if ($item['product_sheet_url'] != '') { ?>
                </a>
              <?php } ?>
            <div class="rounded-15 py-2 px-2 mb-3" style="background-color: <?= $item['brand_bg_color'] ?> ">
              <?php foreach ($item['plays'] as $k => $play) {
                if ($play !== false) { ?>
                  <div class="d-flex pb-1" style="line-height:25px;font-size:0.875rem;">
                  <div class="mr-1" style="min-width:30px;">
                      <?= $play['label'] ?>
                    </div>
                    <div class="product-plays fs112">
                      <?= $play['details'] ?>
                    </div>
                  </div>
                  <p class="position-relative toggleProductListingDetails__content sub-details-infos" style="padding-left:40px;">
                    <?= $play['description'] ?>
                  </p>
              <?php
                }
              } ?>
            </div>
          <?php
            $cpt++;
          }
          ?>
          
          <div class="results-price d-flex text-center flex-column justify-content-center mt-auto pt-1">
            <?php if ($result['result_summary']['quality_score'] != '') { ?>
              <div class="cursor-pointer modalClick mb-3" data-toggle="modal" data-target="#modalQuality">
                <?= $result['result_summary']['quality_score']; ?>
                <span class="cursor-pointer position-absolute ml-2">
                  <i class="fa fa-info pl-2"></i>
                </span>
              </div>
            <?php } ?>
            <p class="mb-2" style="min-height: 80px; line-height: 28px">
              <?php echo $result['result_summary']['displayed_price']; ?>
            </p>
            <div class="setup-wrapper mb-1">
              <div class="mb-0">
                <?= $result['result_summary']['setup']; ?>
              </div>
              <?php if (!empty($result['result_summary']['products_total_savings'])) { ?>
                <p class="total-savings modalClick cursor-pointer mb-0" data-toggle="modal" data-target="#modalTotalSavings">
                  <?= $result['result_summary']['products_total_savings'] ?>
                  <span class="position-absolute">
                    <i class="fa fa-info pl-2"></i>
                  </span>
                </p>
              <?php } ?>
              <?php if ((!empty($result['result_summary']['phone_plug']) || !empty($result['result_summary']['max_activation_time'])) && !self::isOnlyMobile($result)) { ?>
                <div class="position-relative sub-details-infos toggleProductListingDetails__content">
                  <?php if (!empty($result['result_summary']['max_activation_time'])) { ?>
                    <?= $result['result_summary']['max_activation_time']; ?>
                    <?php if (!empty($result['result_summary']['phone_plug'])) { ?>
                      <br>
                    <?php } ?>
                  <?php } ?>
                  <?php if (!empty($result['result_summary']['phone_plug'])) { ?>
                    <?= $result['result_summary']['phone_plug'] ?>
                  <?php } ?>
                </div>
              <?php } ?>
            </div>
            <div class="my-3">
              <?= $result['result_summary']['order_url']; ?>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</div>