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

<div class="container mt-5">

    <div class="d-flex align-items-center mr-3 text-lighter text-uppercase" style="right:0; z-index:999">
      <?= __d('CompareAstelBe', 'switch_resume')?>
        <input class="mx-2 switch-toggle-details" id="toggle-details-<?= $key ?>" type="checkbox">
      <?= __d('CompareAstelBe', 'switch_details') ?>
    </div>

    <div class="row mt-4">
        <?php foreach ($params['results'] as $key => $result) { ?>
            <div class="col-md-3 mb-5">
                <div class="mt-n3 ml-3 py-2 pl-4 cursor-pointer position-absolute rounded-sm plugin-hidden-optional-element cashback-amount modalClick "
                    data-toggle="modal"
                    data-target="#pluginModalCashback"
                    style="color:#fff; background-color: #f23078; left: 95px; width: 160px; font-size: 1.125rem;"
                >
                    <?= $result['result_summary']['total_cashback']?>
                </div>
                <div class="px-3 py-4 shadow rounded-lg d-flex h-100 flex-column justify-content-between">
                    <div class="mt-2">
                        <?php foreach ($result['products'] as $key => $item) { ?>
                            <div class="pb-3 mb-2 rounded" style="background-color: #f5f5f5">
                                <div>
                                    <h2 class="pt-1 px-1 text-<?= $item['brand_slug']; ?>" style="font-size:1.5rem"><?= $item['brand_name']; ?></h2>
                                    <h3 class="px-1 text-<?= $item['brand_slug']; ?>" style="min-height: 46px; font-size: 1.1rem; color: #f23078"><?= $item['short_name']; ?></h3>
                                </div>
                                <div class="pt-1 px-1">
                                    <?php foreach ($item['plays'] as $k => $play) {
                                        if ($play !== false){ ?>
                                            <div class="d-flex align-items-baseline mb-2" style="line-height:25px;font-size:0.875rem;">
                                                <div class="mr-2">
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
                    <div class="results-wrapper text-center">
                        <div class="results-price d-flex flex-column justify-content-center">
                            <div class="cursor-pointer modalClick" data-toggle="modal" data-target="#modalQuality" style="display:inline-block">
                                <?=$result['result_summary']['quality_score'];?>
                                <span class="cursor-pointer position-absolute">
                                    <i class="fa fa-info pl-2"></i>
                                 </span>
                            </div>
                                <div class="">
                                    <?=$result['result_summary']['total_price'];?>
                                    <?=$result['result_summary']['total_price_without_discount'];?>
                                </div>
                                <div class="setup-wrapper">
                                    <p>
                                    <p class="mb-n1 font-weight-bold" style="font-size: 1.2rem">Activation et Installation</p>
                                    <?=$result['result_summary']['reduced_total_setup_price'];?>
                                    <?=$result['result_summary']['full_total_setup_price'];?>
                                    </p>
                                </div>
                                <?=$result['result_summary']['products_total_savings']?>
                                <div class="pt-4 mt-5" style="margin-top: 120px;">
                                    <?=$result['result_summary']['order_url'];?>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
    <?php
    }
        ?>
	</div>
</div>

