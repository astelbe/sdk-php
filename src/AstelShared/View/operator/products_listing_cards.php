<?php
use CakeUtility\Hash;
/*
	This template received $params :	array of results with products and pricings
	$params = [
        0 =>[
            'products' => [
                [
                    'brand_name' => 'Proximus',
                    'short_name' => 'Tuttimus',
                    '...' => ...,
                ],
            ],
            'result_summary' => [
                    'price' => 99,
                    '...' => ...,
            ],
        ],
        ...
    ];
*/

?>

<div class="container px-0 mt-5">

    <div class="d-flex align-items-center mr-3 mb-5 text-lighter text-uppercase" style="right:0; z-index:999">
      <?= __d('CompareAstelBe', 'switch_resume')?>
        <input class="mx-2 switch-toggle-details" id="toggle-details-<?= $key ?>" type="checkbox">
      <?= __d('CompareAstelBe', 'switch_details') ?>
    </div>

    <div class="row mt-4 no-gutters">
        <?php foreach ($params['results'] as $key => $result) { ?>
            <div class="col-12 col-xl-3 col-lg-4 col-md-6 mb-5 px-1 mb-5 mt-2">
                <div class="px-2 py-4 shadow rounded-lg d-flex h-100 flex-column justify-content-between">
                    <div class="mt-n3 ml-3 py-2 pl-4 shadow cursor-pointer position-absolute rounded-sm plugin-hidden-optional-element cashback-amount modalClick "
                        data-toggle="modal"
                        data-target="#pluginModalCashback"
                        style="color:#fff; background-color: #f23078; top:-8px; right: 0.75rem; width: 160px; font-size: 1.125rem;"
                    >
                        <?= $result['result_summary']['total_cashback']?>
                    </div>
                    <div class="mt-2">
                        <?php
                        $cpt = 1; // For displaying "+"
                        foreach ($result['products'] as $key => $item) {

                            // Display "+" between products
                            if($cpt > 1) { ?>
                                <div class="w-100 text-center mb-2">
                                    <i class="fa fa-plus" style="color:#c1c1c1" aria-hidden="true"></i>
                                </div>
                            <?php }
                            $cpt++;

                            ?>
                            <div class="py-2 mb-2 rounded" style="background-color: #f5f5f5">
                                <div>
                                    <h2 class="pt-1 px-1 d-flex justify-content-between text-<?= $item['brand_slug']; ?>" style="font-size:1.5rem">
                                      <?= $item['brand_name']; ?>
                                        <span class=""><?= self::getDisplayedProductCount($item) ?></span>

                                    </h2>
                                    <h3 class="px-1" style="min-height: 46px; font-size: 1.1rem;">
                                      <span class="text-<?= $item['brand_slug']; ?>">
                                        <?= $item['short_name']; ?>
                                      </span>
                                    </h3>
                                </div>
                                <div class="pt-1 px-1">
                                    <?php foreach ($item['plays'] as $k => $play) {
                                        if ($play !== false){ ?>
                                            <div class="d-flex align-items-baseline mb-2" style="line-height:25px;font-size:0.875rem;">
                                                <div class="mr-1">
                                                    <span style="display:inline-block; width:35px"><?= $play['label']?></span>
                                                </div>
                                                <div>
                                                    <?= $play['details']?>
                                                </div>
                                            </div>
                                            <p class="sub-details-infos toggle-details toggle-details-'. $key .'">
                                                <?= $play['description']?>
                                            </p>
                                        <?php
                                        }
                                    } ?>
                                </div>
                            </div>
                        <?php
                        }
                    ?>
                    </div>
                    <div class="results-price d-flex text-center flex-column justify-content-center">
                        <div class="cursor-pointer modalClick" data-toggle="modal" data-target="#modalQuality" style="display:inline-block">
                            <?=$result['result_summary']['quality_score'];?>
                            <span class="cursor-pointer position-absolute">
                                <i class="fa fa-info pl-2"></i>
                             </span>
                        </div>
                        <div class="mb-2">
                            <?=$result['result_summary']['total_price'];?>
                            <?=$result['result_summary']['total_price_without_discount'];?>
                        </div>
                        <div class="setup-wrapper">
                            <p class="mb-n1">
                                <?=$result['result_summary']['setup'];?>
                            </p>

                        </div>
                        <?=$result['result_summary']['products_total_savings']?>
                        <div class="mt-2">
                            <?=$result['result_summary']['order_url'];?>
                        </div>
                    </div>
                </div>
            </div>
    <?php
    }
        ?>
	</div>
</div>

