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
?>

<div class="container px-0 toggleProductListingDetails__container" id="toggleProductListingDetails__container_<?= $params['id'] ?>">
  <div class="d-md-flex justify-content-between align-items-center results-header p-1" style="background-image: linear-gradient(to right, rgb(237, 241, 245) , rgb(237, 241, 245), rgb(255, 255, 255, 1));">
    <h2 class="mt-2 pl-2">
      <?= $params['title']; ?>
    </h2>
    <div class="btn btn-outline-secondary text-uppercase cursor-pointer d-flex justify-content-center text-nowrap toggleProductListingDetails__button" id="toggle-product-listing-button-<?= $params['id'] ?>" onclick="toggleProductListingCards('<?= $params['id'] ?>')">
      <div class="details-hidden">
        <?= self::getTranslation(['cake' => 'CompareAstelBe', 'front' => 'product'], 'switch_details', $this->version)?>&nbsp;<i class="fa fa-chevron-down ml-2" aria-hidden="true"></i>
      </div>
      <div class="details-visible">
        <?= self::getTranslation(['cake' => 'CompareAstelBe', 'front' => 'product'], 'switch_resume', $this->version)?>&nbsp;<i class="fa fa-chevron-up ml-2" aria-hidden="true"></i>
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
          <div class="mb-0 pb-0 rounded" style="background-color: #f5f5f5">

            <?php
                                // Display brand name only if 1st product , and also 2dn result if multi brand result
                                if($cpt == 1 || ($cpt == 2 && $params['id'] == 'view_multi_brand')) { 
                                    $productTitles = $result['result_summary']['product_titles'][$item['brand_name']];
                                  ?>
            <div class="titleproduct-logo-brand mb-n3 p-2 mb-0">
              <img class="w-100" src="<?= $item['brand_logo'] ?>" alt="<?= $item['brand_name'] ?>" title="<?= $productTitles ?>">
            </div>

            <?php } ?>
            <?php if ($item['product_sheet_url'] != ''){ ?>
            <a class="gtm-product-detail-link" href="<?= $item['product_sheet_url'] ?>"
              title="<?= $item['name'] ?>" target="_blank" data-name="<?= $item['short_name']  ?>"
              data-brand="<?= $item['brand_name'] ?>">
              <?php } ?>
              <h3 class="px-1 pt-3 mb-n2 d-flex justify-content-between" style="min-height: 46px; font-size: 1.1rem;"
                <?= ($cpt == 1 ? 'style="min-height: 46px; font-size: 1.1rem;"' : '') ?>>
                <span class="text-<?= $item['brand_slug']; ?> font-weight-bold">
                  <?= $item['short_name']; ?>
                </span>
                <span class="font-weight-bold" style="1.2rem;"><?= self::getDisplayedProductCount($item) ?></span>
              </h3>
              <?php if ($item['product_sheet_url'] != ''){ ?>
            </a>
            <?php } ?>
            <div class="px-1 mb-1 mt-2">
              <?php foreach ($item['plays'] as $k => $play) {
                                        if ($play !== false){ ?>
              <div class="d-flex align-items-baseline pb-1" style="line-height:25px;font-size:0.875rem;">
                <div class="mr-1 pt-2">
                  <span style="display:inline-block; width:35px"><?= $play['label']?></span>
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
              <?php if(!empty($result['result_summary']['phone_plug']) || !empty($result['result_summary']['max_activation_time'])) { ?>
                <div class="position-relative sub-details-infos toggleProductListingDetails__content">
                  <?php if(!empty($result['result_summary']['max_activation_time'])) { ?>
                      <?=$result['result_summary']['max_activation_time'];?>
                      <?php if(!empty($result['result_summary']['phone_plug'])) { ?>
                          <br>
                      <?php } ?>
                  <?php } ?>
                  <?php if(!empty($result['result_summary']['phone_plug'])) { ?>
                      <?= $result['result_summary']['phone_plug']?>
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
