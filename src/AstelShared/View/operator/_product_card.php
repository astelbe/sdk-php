<?php

use AstelShared\Translate\Translate;
use CakeUtility\Hash;
// debug($result);
?>

<div class="product-card position-relative mb-3<?= $isCurrent ? ' current-highlight' : '' ?>">

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
  <div class="px-3 pt-4 pb-2 rounded-15 d-flex h-100 flex-column justify-content-between align-item-end information-box">
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
          title="<?= $item['brand_name']; ?> <?= $item['name'] ?>" <?= ($params['product_link_in_new_tab'] == true ? ' target="_blank"' : '') ?> data-name="<?= $item['name'] ?>"
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
          }
        } ?>
        <a href="#" onclick="toggleProductListingCards('<?= $result['id'] ?>'); return false;"
          role="button"
          aria-expanded="false"
          aria-controls="toggleProductListingDetails__content"
          class="d-block w-100 p-0 text-center text-blue toggleProductListingDetails">
          <span class="showDetails">
            <?= Translate::get('switch_details'); ?> <i class="fa fa-chevron-down"></i>
          </span>
          <span class="hideDetails d-none">
            <?= Translate::get('hide_details'); ?> <i class="fa fa-chevron-up"></i>
          </span>

        </a>
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
            <?php } ?>
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