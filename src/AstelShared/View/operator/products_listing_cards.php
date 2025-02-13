<?php

use AstelShared\Translate\Translate;
use CakeUtility\Hash;
use AstelShared\SharedView;

$SharedView = SharedView::getInstance();
// debug($SharedView);

// $translator = Translate::getInstance();


/*
  From operator pages public_html_v1/www/public_html/app/view/helper/ProductsListingCards.php
  From COMP in comparator-engine
  From ORDER form in mobile-options

  This listings is composed of cards. (productCard)
  Every cards is composed of one or more products (one in operator pages and more in comparator results) and a a card summary of the product(s)
  This template received $params :	array of productCards with products and pricings
  $params = [
        'title' => 'Title of the result',
        'id' => 'list_id', // for toggle details
        'product_link_in_new_tab' => true, // default false, to open product link in new tab
        'options' => [
            'display_operator_in_product_name' => true/false, // default true, to noyt display logo in operator page
        'productCards' => [
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
// debug($params);
?>

<div class="container px-0 toggleProductListingDetails__container"
  id="toggleProductListingDetails__container_<?= $params['id'] ?>">
  <div
    class="d-flex flex-xl-row justify-content-between align-items-start align-items-xl-center<?= (!empty($params['title']) ? ' bg-lightblue border-blue' : '') ?> p-2 brad100 g100 information-box">
    <?php
    if (isset($params['title'])) {
    ?>
      <h2 class="m-0 fs125">
        <?= $params['title']; ?>
      </h2>
    <?php
    }
    ?>

    <div
      class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-center justify-content-sm-between w-100 w-sm-auto">
      <?php
      if ($params['display_best_seller_filter'] == 1) {
      ?>
        <div class="d-flex g100 mr-0 mr-sm-4">
          <?php
          $currentUrl = $params['url'];
          // Remove any existing query parameters
          $baseUrl = strtok($currentUrl, '?');

          // URLs for the links
          $bestsellersUrl = $baseUrl;
          $allUrl = $baseUrl . "?display=all";
          ?>
          <a href="<?= htmlspecialchars($bestsellersUrl); ?>"
            class="underlineWhenHovered <?= $currentUrl === $bestsellersUrl ? 'fw700' : 'fw400'; ?> text-darkblue text-nowrap"
            rel="nofollow">
            <?= Translate::get('ficheOperateur_display_bestsellers'); ?>
          </a>
          <a href="<?= htmlspecialchars($allUrl); ?>"
            class="underlineWhenHovered <?= $currentUrl === $allUrl ? 'fw700' : 'fw400'; ?> text-darkblue text-nowrap"
            rel="nofollow">
            <?= Translate::get('ficheOperateur_display_all'); ?>
          </a>
        </div>
      <?php
      }
      ?>

      <div class="d-flex align-items-center toggleProductListingDetails mt-2 mt-sm-0">
        <input type="checkbox" class="toggleProductListingDetails__button mr-2"
          id="toggle-product-listing-button-<?= $params['id'] ?>"
          onclick="toggleProductListingCards('<?= $params['id'] ?>')">
        <label for="toggle-product-listing-button-<?= $params['id'] ?>"
          class="m-0 toggleProductListingDetails__detailsLabel cursor-pointer">
          <?= Translate::get('switch_details'); ?>
        </label>
      </div>
    </div>
  </div>

  <div class="gridcontainer gridcontainer_listing g100 mt-4 mb-4" style="gap-row:1.2rem;">
    <?php
    // LOOP ON PRODUCT CARDS
    foreach ($params['productCards'] as $key => $result) {
      $cashback = ($result['result_summary']['total_cashback'] != '' && $result['result_summary']['total_cashback'] !== 0 && $result['cashback_source'] != 'None') ? $result['result_summary']['total_cashback'] : false;
    ?>
      <div class="product-card position-relative mb-3">
        <?php if (isset($result['result_index'])) { 
          ?>
          <div class="result-index ml-2">
            <?= $result['result_index'] ?>
          </div>
          <?php } else {
            $result_index = $key + 1; ?>
            <div class="result-index ml-2">
              <?= $result_index  ?>
            </div>
        <?php } ?>
        <?php if ($cashback) { ?>
          <div
          class="mt-n3 ml-3 py-0 px-3 cursor-pointer position-absolute rounded-sm plugin-hidden-optional-element cashback-amount bg-pink hoverUnderline modalClick "
          data-toggle="modal" data-target="#pluginModalCashback"
          style="color:#fff; top:2px; height:32px; line-height: 32px; right: 0.75rem; font-size: 0.9rem;">
          <?= $cashback ?> <i class="fa fa-info pl-1" style="font-size:1rem"></i>
        </div>
        <?php } ?>
        <div class="px-3 pt-4 pb-2 rounded-15 d-flex h-100 flex-column justify-content-between align-item-end"
          style="box-shadow: 0 2px 30px 0 rgba(0, 0, 0, 0.1);">
          <?php
          $cpt = 1; // To display "+"
          foreach ($result['products'] as $subkey => $item) {
            if ($cpt > 1) { ?>
              <svg class="w-100 mb-2" width="260" height="15" viewBox="0 0 260 30" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <rect x="128" width="4" height="30" fill="#1F438C" />
                <rect x="115" y="13" width="30" height="4" fill="#1F438C" />
              </svg>
            <?php } ?>
            <?php
            // Display brand name only if 1st product , and also 2dn result if multi brand result
            $productTitles = $result['result_summary']['product_titles'][$item['brand_name']];
            if (($cpt == 1 || ($cpt == 2 && $params['id'] == 'view_multi_brand')) && $params['options']['display_operator_in_product_name'] !== false) { ?>
              <div class="my-2 text-center" style="height: 30px">
                <img src="<?= $item['brand_logo'] ?>" alt="<?= $item['brand_name'] ?>" style="max-height:24px;"
                  title="<?= $productTitles ?>">

              </div>
            <?php } ?>
            <?php if ($item['product_sheet_url'] != '') { 
              ?>
              <a class="gtm-product-detail-link" href="<?= $item['product_sheet_url'] ?>"
                title="<?= $item['brand_name']; ?> <?= $item['name'] ?>"<?= ($params['product_link_in_new_tab'] == true ? ' target="_blank"' : '') ?> data-name="<?= $item['name'] ?>"
                data-brand="<?= $item['brand_name'] ?>">
              <?php } ?>
              <h3 class="px-1 d-flex underlineWhenHovered text-<?= $item['brand_slug']; ?>"
                style="font-size: 1.1rem!important;<?= ($cpt == 1 ? 'min-height: 46px;' : '') ?>">
                <span class="font-weight-bold" style="1.2rem;"><?= self::getDisplayedProductCount($item) ?></span>
                <span class="text-<?= $item['brand_slug']; ?> mobile-underline">
                  <?= $item['brand_name']; ?> <?= $item['short_name']; ?>
                </span>
              </h3>
              <?php if ($item['product_sheet_url'] != '') { ?>
              </a>
            <?php } ?>
            <div class="rounded-15 py-2 px-2 mb-2" style="background-color: <?= $item['brand_bg_color'] ?> ">
              <?php
              // PLAYS DESCRIPTION
              foreach ($item['plays'] as $k => $play) {
                if ($play !== false) { ?>
                  <div class="d-flex pb-1 align-items-center" style="line-height:25px;font-size:0.875rem;">
                    <div class="mr-1" style="min-width:30px;">
                      <?= $play['label'] ?>
                    </div>
                    <div class="product-plays fs100">
                      <?= $play['details'] ?>
                    </div>
                  </div>
                  <p class="position-relative toggleProductListingDetails__content sub-details-infos"
                    style="padding-left:40px;">
                    <?= $play['description'] ?>
                  </p>
              <?php
                  // echo $item['total_savings'];
                }
              } ?>
            </div>
          <?php
            $cpt++;
          }
          ?>

          <div class="results-price d-flex text-center flex-column justify-content-center mt-auto pt-1">
            <?php
            // QUALITY SCORE
            if ($result['result_summary']['quality_score'] != '') { ?>
              <div class="cursor-pointer modalClick mb-2" data-toggle="modal" data-target="#modalQuality">
                <?= $result['result_summary']['quality_score']; ?>
                <span class="cursor-pointer position-absolute ml-2">
                  <i class="fa fa-info pl-2"></i>
                </span>
              </div>
            <?php } 
            // PRICE
            ?>
            <p class="my-1" style="min-height: 60px; line-height: 20px">
              <?php echo $result['result_summary']['displayed_price']; ?>
            </p>
            <div class="setup-wrapper mb-2">
              <div class="mb-0">
              <?php echo $result['result_summary']['setup']; ?>
              </div>
              <?php
              // PRODUCT TOTAL SAVINGS
              if (!empty($result['result_summary']['products_total_savings'])) { ?>
                <p class="total-savings modalClick hoverUnderline cursor-pointer mb-0" data-toggle="modal"
                  data-target="#modalTotalSavings<?= $params['id'] ?>">
                  <?= $result['result_summary']['products_total_savings'] ?>
                  <?= $result['products']['total_savings'] ?>
                  <span class="position-absolute">
                    <i class="fa fa-info pl-2"></i>
                  </span>
                </p>
              <?php } ?>
              <?php
              // MAX ACTIVATION TIME & INTERNET PLUG - Product details
              if ((!empty($result['result_summary']['plug']) || !empty($result['result_summary']['max_activation_time']))) { ?>
                <div class="position-relative sub-details-infos toggleProductListingDetails__content">
                  <?php if (!empty($result['result_summary']['max_activation_time'])) { ?>
                    <p class="fs087 my-1">
                      <?= $result['result_summary']['max_activation_time']; ?>
                  </p>
                  <?php } ?>
                  <?php if (!empty($result['result_summary']['phone_plug'])) { ?>
                    <!-- <p class="mb-1"> -->
                      <?= $result['result_summary']['phone_plug']; ?>
                    <!-- </p> -->
                  <?php }?>
                </div>
              <?php } ?>
            </div>
            <div class="my-1">
              <?php // ORDER BUTTON 
              ?>
              <?= $result['result_summary']['order_button']; ?>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</div>
<!-- Modal Total Savings -->
<div class="modal fade" id="modalTotalSavings<?= $params['id'] ?>" tabindex="-1" role="dialog"
  aria-labelledby="modal total savings" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <?= Translate::get('total_savings_modal_title'); ?>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <?= Translate::get('total_savings_modal_content'); ?>
      </div>
    </div>
  </div>
</div>

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

  .gridcontainer_listing {
    grid-template-columns: repeat(4, 1fr);
  }

  @media screen and (max-width: 1200px) {
    .gridcontainer_listing {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media screen and (max-width: 576px) {
    .gridcontainer_listing {
      grid-template-columns: 1fr;
    }
  }

  @media screen and (min-width: 576px) {
    .w-sm-auto {
      width: auto !important;
    }
  }

  @media screen and (max-width: 720px) {
    .information-box {
      box-shadow: 0 2px 30px 0 rgba(0, 0, 0, 0.3);
    }

    .mobile-underline {
      text-decoration: underline;
    }
  }
</style>