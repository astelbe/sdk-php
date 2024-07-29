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
debu
*/

// debug($params['products'][0]['products'][1905]['plays']);
?>
<!-- <?php debug($params); ?> -->
<div class="container px-0 toggleProductListingDetails__container" id="toggleProductListingDetails__container_<?= $params['id'] ?>">
  <div class="d-flex flex-column flex-xl-row justify-content-between align-items-start align-items-xl-center bg-lightblue p-2 brad100 g100">
    <h2 class="m-0 fs125">
      <?= $params['title']; ?>
    </h2>

    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-center justify-content-sm-between w-100 w-sm-auto">
      <div class="d-flex g100">
        <!-- <p class="m-0 mr-3 fs150"><?= __d('pages', 'display') ?> :</p> -->
        <a href="" class="underlineWhenHovered fw700 text-darkblue text-nowrap" rel="nofollow">
          Bestsellers uniquement
        </a>
        <a href="" class="underlineWhenHovered fw400 text-darkblue text-nowrap" rel="nofollow">
          Afficher tout
        </a>
      </div>

      <div class="d-flex align-items-center toggleProductListingDetails mt-2 mt-sm-0 ml-0 ml-sm-4">
        <input type="checkbox" class="toggleProductListingDetails__button mr-2" id="toggle-product-listing-button-<?= $params['id'] ?>" onclick="toggleProductListingCards('<?= $params['id'] ?>')">

        <label for="toggle-product-listing-button-<?= $params['id'] ?>" class="m-0 toggleProductListingDetails__detailsLabel cursor-pointer">
          <?= self::getTranslation(['cake' => 'CompareAstelBe', 'front' => 'product'], 'switch_details', $this->version) ?>
        </label>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <?php
    foreach ($params['products'] as $key => $result) {
      $cashback = ($result['result_summary']['total_cashback'] != '' && $result['result_summary']['total_cashback'] !== 0 && $result['cashback_source'] != 'None') ? $result['result_summary']['total_cashback'] : false;
    ?>
      <div class="col-12 col-xl-3 col-lg-4 col-md-6 product-card mb-3 px-2">

        <div class="px-3 pt-3 pb-2 rounded-15 d-flex h-100 flex-column justify-content-between align-item-end" style="box-shadow: 0 2px 30px 0 rgba(0, 0, 0, 0.1);">
          <?php if ($cashback) { ?>
            <div class="py-2 px-3 mb-3 text-center cursor-pointer rounded-xl plugin-hidden-optional-element cashback-amount modalClick bg-pink" data-toggle="modal" data-target="#pluginModalCashback" style="color:#fff; margin:0 auto;">
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
                <rect x="128" width="4" height="30" fill="#1F438C" />
                <rect x="115" y="13" width="30" height="4" fill="#1F438C" />
              </svg>
            <?php } ?>
            <?php
            // Display brand name only if 1st product , and also 2dn result if multi brand result
            $productTitles = $result['result_summary']['product_titles'][$item['brand_name']];
            if (($cpt == 1 || ($cpt == 2 && $params['id'] == 'view_multi_brand')) && $params['options']['display_operator_in_product_name'] !== false) { ?>
              <div class="my-2 text-center" style="height: 36px">
                <img src="<?= $item['brand_logo'] ?>" alt="<?= $item['brand_name'] ?>" style="max-height:28px;" title="<?= $productTitles ?>">

              </div>
            <?php } ?>
            <?php if ($item['product_sheet_url'] != '') { ?>
              <a class="gtm-product-detail-link" href="<?= $item['product_sheet_url'] ?>" title="<?= $item['brand_name']; ?> <?= $item['name'] ?>" target="_blank" data-name="<?= $item['name']  ?>" data-brand="<?= $item['brand_name'] ?>">
              <?php } ?>
              <h3 class="px-1 d-flex underlineWhenHovered text-<?= $item['brand_slug']; ?>" style="font-size: 1.1rem;<?= ($cpt == 1 ? 'min-height: 46px;' : '') ?>">
                <span class="font-weight-bold" style="1.2rem;"><?= self::getDisplayedProductCount($item) ?></span>
                <span class="text-<?= $item['brand_slug']; ?>">
                  <?= $item['brand_name']; ?> <?= $item['short_name']; ?>
                </span>
              </h3>
              <?php if ($item['product_sheet_url'] != '') { ?>
              </a>
            <?php } ?>
            <div class="rounded-15 py-2 px-2 mb-3" style="background-color: <?= $item['brand_bg_color'] ?> ">
              <?php foreach ($item['plays'] as $k => $play) {
                if ($play !== false) { ?>
                  <div class="d-flex pb-1 align-items-center" style="line-height:25px;font-size:0.875rem;">
                    <div class="mr-1" style="min-width:30px;">
                      <?= $play['label'] ?>
                    </div>
                    <div class="product-plays fs100">
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
                  <?php
                  }
                  ?>
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
<!-- test -->

<style>
  .toggleProductListingDetails {
    width: fit-content;
    padding: 0.5rem;
    border-radius: 0.5rem;
  }

  .toggleProductListingDetails:has(input[type="checkbox"]:checked) {
    background-color: var(--darkblue);
    color: #fff;
  }

  .toggleProductListingDetails:has(input[type="checkbox"]:not(:checked)) {
    background-color: var(--lightblue);
    color: var(--darkblue);
    outline: 1px solid var(--blue);
  }

  .toggleProductListingDetails>input[type="checkbox"]:not(:checked) {
    outline: 1px solid var(--blue);
    background-color: #fff;
  }

  .toggleProductListingDetails>input[type="checkbox"] {
    appearance: none;
    height: 1.5rem;
    width: 1.5rem;
    padding: 0.25rem;
    border: none;
    border-radius: 25%;
    position: relative;
  }

  .toggleProductListingDetails>input[type="checkbox"]::after {
    display: block;
    position: absolute;
    content: "";
    background-color: var(--darkblue);
    transform: rotate(45deg);
    width: 0.5rem;
    height: 1rem;
    box-shadow: inset -0.125rem -0.125rem 0 0.1rem #fff;
    left: 0.5rem;
    transition: all 0.1s ease-in-out;
  }

  .toggleProductListingDetails>input[type="checkbox"]:not(:checked)::after {
    scale: 0;
  }

  /* .toggleProductListingDetails:has(input[type="checkbox"]:not(:checked)) > .toggleProductListingDetails__detailsLabel {
    display: none;
  } */

  /* .toggleProductListingDetails:has(input[type="checkbox"]:checked) > .toggleProductListingDetails__resumeLabel {
    display: none;
  } */

   @media screen and (min-width: 576px) {
    .w-sm-auto {
      width: auto !important;
    }
    
   }
</style>