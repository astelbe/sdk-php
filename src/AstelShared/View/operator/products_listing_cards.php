<?php

use CakeUtility\Hash;
// debug($params);

/*
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


<section class="productCards-gridContainer">
  <?php
  // debug($params['results']);
  foreach ($params['results'] as $key => $result) {
    $cashback = ($result['result_summary']['total_cashback'] != '' && $result['result_summary']['total_cashback'] !== 0) ? $result['result_summary']['total_cashback'] : false;
    $cpt = 1; // To display "+" between products
  ?>
    <div class="productCard">

      <?php
      foreach ($result['products'] as $key => $item) :
        // debug($item);
        // debug($item);
        if ($cpt > 1) {
          echo
          '<svg width="260" height="30" viewBox="0 0 260 30" fill="none" xmlns="http://www.w3.org/2000/svg">
            <line y1="15.5" x2="110" y2="15.5" stroke="#D8D8D8"/>
            <line x1="150" y1="15.5" x2="260" y2="15.5" stroke="#D8D8D8"/>
            <rect x="126" width="8" height="30" fill="#6CC1F0"/>
            <rect x="115" y="11" width="30" height="8" fill="#6CC1F0"/>
          </svg>';
        } else {
          echo $cashback ? "<div class='productCard-cashback productCard-cell'>{$cashback}</div>" : "";
        }
      ?>



        <img src="<?= $item['brand_logo'] ?>" alt="<?= $item['brand_name'] ?>" class="productCard-brandLogo">

        <!-- <div class="productCard-typesIcons productCard-cell"><?= $params['fa-icon'] ?></div> -->

        <h3 class="productCard-title"><?= $item['short_name'] ?></h3>

        <div class="productCard-description productCard-cell" style="background-color: <?= $item['brand_bg_color'] ?> ">
          <?php foreach ($item['plays'] as $k => $play) {
            if ($play !== false) { ?>
              <span>
                <?= $play['label'] ?>

                <?= $play['details'] ?>

                <!-- <?php debug($play); ?> -->

                <br>
              </span>

              <!-- <p>
              <?= $play['description'] ?>
            </p> -->
          <?php
            }
          }

          $cpt++;
          ?>

        </div>
      <?php
      endforeach;
      ?>

      <div><?php echo $result['result_summary']['displayed_price']; ?></div>

      <?=
      $result['result_summary']['setup'] ? "<div>{$result['result_summary']['setup']}</div>" : ""
      ?>

      <button class="blueBtn darkBlueBtn">Commander</button>

      <a href="#" class="productCard-detailsLink">Détails →</a>
    </div>

  <?php
  }
  ?>
</section>

<style>
  .text-delgrey {
    color: #767676;
  }

  .text-darkblue {
    color: #1F438C;
  }

  .fs112 {
    font-size: 1.125rem;
  }

  .fw700 {
    font-weight: 700;
  }

  .productCards-gridContainer {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-top: 5rem;
    margin-bottom: 5rem;
  }

  .productCard {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 1rem;
    border-radius: 1rem;
    box-shadow: 0 2px 30px 0 rgba(0, 0, 0, 0.1);
  }

  .productCard>* {
    margin-bottom: 1rem;
  }

  .productCard-cell {
    padding: 0.5rem;
    padding-left: 2rem;
    padding-right: 2rem;
    border-radius: 0.5rem;
  }

  .productCard-brandLogo {
    width: 50%;
    margin: 2rem;
  }

  .productCard-typesIcons {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #E5F4FE;
    padding-left: 1rem;
    padding-right: 1rem;
    gap: 1rem;
  }

  .productCard-title {
    font-size: 1.25rem;
    font-weight: 700;
    line-height: 150%;
  }

  .productCard-activation {
    display: flex;
    flex-wrap: nowrap;
    background-color: #FFF2F7;
    color: #E5176B;
  }

  .productCard-cashback {
    background-color: #E5176B;
    color: #fff;
  }

  .productCard-description {
    text-align: left;
  }

  .productCard-detailsLink {
    text-decoration: underline;
    transition: all 100ms ease-in-out;
    color: #1CAAF9;
  }






  .blueBtn {
    display: flex;
    justify-content: center;
    align-items: center;
    border: none;
    padding-left: 3rem;
    padding-right: 3rem;
    height: 4rem;
    transition: all 200ms ease-in-out;
  }

  .darkBlueBtn {
    background-color: #1F438C;
    color: #fff;
    border-radius: 2rem;
  }

  .darkBlueBtn:hover {
    background-color: #2451AC;
  }

  .darkBlueBtn:active {
    background-color: #05276B;
  }
</style>


<div class="container px-0 toggleProductListingDetails__container" id="toggleProductListingDetails__container_<?= $params['id'] ?>">
  <div class="d-md-flex justify-content-between align-items-center results-header p-1" style="background-image: linear-gradient(to right, rgb(237, 241, 245) , rgb(237, 241, 245), rgb(255, 255, 255, 1));">
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


  <div class="row mt-4 no-gutters">
    <?php foreach ($params['results'] as $key => $result) {
      $cashback = ($result['result_summary']['total_cashback'] != '' && $result['result_summary']['total_cashback'] !== 0) ? $result['result_summary']['total_cashback'] : false;
    ?>
      <div class="col-12 col-xl-3 col-lg-4 col-md-6 mb-5 px-1 mb-5 mt-4 product-card">
        <?php if ($result['result_index']) { ?>
          <div class="result-index ml-2">
            <?= $result['result_index'] ?>
          </div>
        <?php } ?>
        <div class="px-2 pt-1 pb-2 rounded-lg d-flex h-100 flex-column justify-content-between" style="box-shadow: 2px 0rem 1.2rem rgba(0,0,0,.35)!important">
          <?php if ($cashback) { ?>
            <div class="mt-n3 ml-3 py-0 px-3 shadow cursor-pointer position-absolute rounded-sm plugin-hidden-optional-element cashback-amount modalClick " data-toggle="modal" data-target="#pluginModalCashback" style="color:#fff; background-color: #f23078; top:2px; height:32px; line-height: 32px; right: 0.75rem; font-size: 0.9rem;">
              <?= $cashback ?> <i class="fa fa-info pl-1" style="font-size:1rem"></i>
            </div>
          <?php } ?>
          <div class="<?= $cashback ? 'mt-4' : 'mt-1' ?>">
            <?php
            $cpt = 1; // To display "+"
            foreach ($result['products'] as $key => $item) {
              if ($cpt > 1) { ?>
                <div class="w-100 text-center mb-1">
                  <i class="fa fa-plus" style="color:#878787" aria-hidden="true"></i>
                </div>
              <?php } ?>
              <div class="mb-0 pb-0 rounded" style="background-color: #f5f5f5">

                <?php
                // Display brand name only if 1st product , and also 2dn result if multi brand result
                if (($cpt == 1 || ($cpt == 2 && $params['id'] == 'view_multi_brand')) && $params['options']['display_operator_in_product_name'] !== false) { ?>
                  <div class="titleproduct-logo-brand p-2 mb-0">
                    <img class="w-100" src="<?= $item['brand_logo'] ?>" alt="<?= $item['brand_name'] ?>">
                  </div>
                <?php } ?>
                <?php if ($item['product_sheet_url'] != '') { ?>
                  <a class="gtm-product-detail-link" href="<?= $item['product_sheet_url'] ?>" title="<?= $item['short_name'] ?>" target="_blank" data-name="<?= $item['short_name']  ?>" data-brand="<?= $item['brand_name'] ?>">
                  <?php } ?>
                  <h3 class="px-1 pt-3 d-flex justify-content-between" <?= ($cpt == 1 ? 'style="min-height: 46px; font-size: 1.1rem;"' : '') ?>>
                    <span class="text-<?= $item['brand_slug']; ?>">
                      <?= $item['short_name']; ?>
                    </span>
                    <span class="font-weight-bold" style="1.2rem;"><?= self::getDisplayedProductCount($item) ?></span>
                  </h3>
                  <?php if ($item['product_sheet_url'] != '') { ?>
                  </a>
                <?php } ?>
                <div class="pt-2 px-1 mb-1">
                  <?php foreach ($item['plays'] as $k => $play) {
                    if ($play !== false) { ?>
                      <div class="d-flex align-items-baseline pb-1" style="line-height:25px;font-size:0.875rem;">
                        <div class="mr-1">
                          <span style="display:inline-block; width:35px"><?= $play['label'] ?></span>
                        </div>
                        <div>
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
              </div>
            <?php
              $cpt++;
            }
            ?>
          </div>
          <div class="results-price d-flex text-center flex-column justify-content-center mt-2">
            <?php if ($result['result_summary']['quality_score'] != '') { ?>
              <div class="cursor-pointer modalClick mb-3" data-toggle="modal" data-target="#modalQuality">
                <?= $result['result_summary']['quality_score']; ?>
                <span class="cursor-pointer position-absolute">
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
            <div class="mt-2">
              <?= $result['result_summary']['order_url']; ?>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>

  <div class="mt-2">
    <?= $result['result_summary']['order_url']; ?>
  </div>
</div>