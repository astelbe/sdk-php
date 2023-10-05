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

<div class="container px-0">

    <div class="d-flex justify-content-end mb-5">
        <div class="btn btn-outline-secondary text-uppercase switch-toggle-details cursor-pointer" id="toggle-details-<?= $params['id'] ?>">
            <div class="details-hidden">
                <?= __d('CompareAstelBe', 'switch_resume')?> <i class="fa fa-chevron-up ml-2" aria-hidden="true"></i>
            </div>
            <div class="details-shown d-none">
                <?= __d('CompareAstelBe', 'switch_details') ?> <i class="fa fa-chevron-down ml-2" aria-hidden="true"></i>
            </div>
        </div>
    </div>

    <div class="row mt-4 no-gutters">
        <?php foreach ($params['results'] as $key => $result) {
//					debug($result);
            ?>
            <div class="col-12 col-xl-3 col-lg-4 col-md-6 mb-5 px-1 mb-5 mt-2 product-card">
							<div class="result-index">
								<?= $key += 1; ?>
							</div>
                <div class="px-2 py-4 shadow rounded-lg d-flex h-100 flex-column justify-content-between">
                    <div class="mt-n3 ml-3 py-1 px-4 shadow cursor-pointer position-absolute rounded-sm plugin-hidden-optional-element cashback-amount modalClick "
                        data-toggle="modal"
                        data-target="#pluginModalCashback"
                        style="color:#fff; background-color: #f23078; top:-8px; right: 0.75rem; font-size: 0.9rem;"
                    >
                        <?= $result['result_summary']['total_cashback']?>
                    </div>
                    <div class="mt-2">
                        <?php
                        $cpt = 1; // To display "+"
                        foreach ($result['products'] as $key => $item) {
 													// Display "+" between products
                            if($cpt > 1) { ?>
                                <div class="w-100 text-center mb-2">
                                    <i class="fa fa-plus" style="color:#c1c1c1" aria-hidden="true"></i>
                                </div>
                            <?php }
                            $cpt++;

                            ?>
                            <div class="mb-2 rounded" style="background-color: #f5f5f5">
                                <h2 class="pb-1 pt-2 px-1 d-flex justify-content-between text-white rounded-top  bg-<?= $item['brand_slug']; ?>" style="font-size:1.5rem">
                                  <?= $item['brand_name']; ?>
                                    <span class=""><?= self::getDisplayedProductCount($item) ?></span>

                                </h2>
                                <h3 class="px-1" style="min-height: 46px; font-size: 1.1rem;">
                                  <span class="text-<?= $item['brand_slug']; ?>">
                                    <?= $item['short_name']; ?>
                                  </span>
                                </h3>
                                <div class="pt-1 px-1 mb-2">
                                    <?php foreach ($item['plays'] as $k => $play) {
                                        if ($play !== false){ ?>
                                            <div class="d-flex align-items-baseline " style="line-height:25px;font-size:0.875rem;">
                                                <div class="mr-1">
                                                    <span style="display:inline-block; width:35px"><?= $play['label']?></span>
                                                </div>
                                                <div>
                                                    <?= $play['details']?>
                                                </div>
                                            </div>
                                            <p class="position-relative sub-details-infos toggle-details toggle-details-<?= $params['id'] ?>" style="padding-left:40px;">
                                                <?= $play['description'] ?>
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
                    <div class="results-price d-flex text-center flex-column justify-content-center mt-2">
                        <div class="cursor-pointer modalClick mb-3" data-toggle="modal" data-target="#modalQuality">
                            <?=$result['result_summary']['quality_score'];?>
                            <span class="cursor-pointer position-absolute">
                                <i class="fa fa-info pl-2"></i>
                             </span>
                        </div>
                        <p class="mb-4">
                            <?php echo $result['result_summary']['displayed_price'];?>
                        </p>
                        <div class="setup-wrapper">
                            <div class="mb-1">
                                <?=$result['result_summary']['setup'];?>
                            </div>
                            <?php if (!empty($result['result_summary']['products_total_savings'])) { ?>
                                <p class="total-savings modalClick cursor-pointer" data-toggle="modal" data-target="#modalTotalSavings">
                                    <?= $result['result_summary']['products_total_savings'] ?>
                                    <span class="position-absolute">
                                        <i class="fa fa-info pl-2"></i>
                                    </span>
                                </p>
                            <?php } ?>
													<p class="position-relative sub-details-infos toggle-details toggle-details-<?= $params['id'] ?>" style="padding-left:40px;">
														<?= $result['result_summary']['phone_plug']?>
													</p>
													
                        </div>

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

