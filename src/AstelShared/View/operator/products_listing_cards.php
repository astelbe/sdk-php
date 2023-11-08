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

    <div class="d-flex justify-content-between align-items-center results-header p-1">
			<h2 class="mt-2 pl-2">
				<?php
				echo $params['title'];
				?>
			</h2>
        <div class="bg-white btn btn-outline-secondary text-uppercase switch-toggle-details cursor-pointer" id="toggle-details-<?= $params['id'] ?>">
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
            ?>
            <div class="col-12 col-xl-3 col-lg-4 col-md-6 mb-5 px-1 mb-5 mt-4 product-card">
                <?php if($result['result_index']) { ?>
                    <div class="result-index ml-2">
                        <?= $result['result_index'] ?>
                    </div>
                <?php } ?>
                <div class="px-2 pt-4 pb-2 rounded-lg d-flex h-100 flex-column justify-content-between" style="box-shadow: 2px 0rem 1.2rem rgba(0,0,0,.35)!important">
                    <div class="mt-n3 ml-3 py-0 px-3 shadow cursor-pointer position-absolute rounded-sm plugin-hidden-optional-element cashback-amount modalClick "
                        data-toggle="modal"
                        data-target="#pluginModalCashback"
                        style="color:#fff; background-color: #f23078; top:2px; height:32px; line-height: 32px; right: 0.75rem; font-size: 0.9rem;"
                    >
                        <?= $result['result_summary']['total_cashback']?> <i class="fa fa-info pl-1" style="font-size:1rem"></i>
                    </div>
                    <div class="mt-3">
                        <?php
                        $cpt = 1; // To display "+"
                        foreach ($result['products'] as $key => $item) {
													
                            if($cpt > 1) { ?>
                                <div class="w-100 text-center mb-1">
                                    <i class="fa fa-plus" style="color:#878787" aria-hidden="true"></i>
                                </div>
                            <?php } ?>
                            <div class="mb-0 pb-0 rounded" style="background-color: #f5f5f5">

                                <?php
                                // Display brand name only if 1st product , and also 2dn result if multi brand result
                                if($cpt == 1 || ($cpt == 2 && $params['id'] == 'view_multi_brand')) { ?>
                                <h2 class="p-2 mb-0 text-center text-white rounded-top  bg-<?= $item['brand_slug']; ?>" style="font-size:1.2rem">
                                  <?= $item['brand_name']; ?>
<!--                                    <span class="">--><?//= self::getDisplayedProductCount($item) ?><!--</span>-->
                                </h2>
																<div class="titleproduct-logo-brand m-0 mb-2">
																	<img class="w-100" src="<?= $item['brand_logo'] ?>" alt="<?= $item['brand_name'] ?>">
																</div>
																	
                                <?php } ?>
                                <h3 class="px-1 pt-3 d-flex justify-content-between" <?= ($cpt == 1 ? 'style="min-height: 46px; font-size: 1.1rem;"' : '') ?>>
                                    <span class="text-<?= $item['brand_slug']; ?>">
                                        <?= $item['short_name']; ?>
                                    </span>
                                    <span class="font-weight-bold" style="1.2rem;"><?= self::getDisplayedProductCount($item) ?></span>
                                </h3>
                                <div class="pt-2 px-1 mb-1">
                                    <?php foreach ($item['plays'] as $k => $play) {
                                        if ($play !== false){ ?>
                                            <div class="d-flex align-items-baseline pb-1" style="line-height:25px;font-size:0.875rem;">
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
                            $cpt++;
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
                        <p class="mb-2" style="min-height: 80px; line-height: 28px">
                            <?php echo $result['result_summary']['displayed_price'];?>
                        </p>
                        <div class="setup-wrapper mb-1">
                            <div class="mb-0">
                                <?=$result['result_summary']['setup'];?>
                            </div>
                            <?php if (!empty($result['result_summary']['products_total_savings'])) { ?>
                                <p class="total-savings modalClick cursor-pointer mb-0" data-toggle="modal" data-target="#modalTotalSavings">
                                    <?= $result['result_summary']['products_total_savings'] ?>
                                    <span class="position-absolute">
                                        <i class="fa fa-info pl-2"></i>
                                    </span>
                                </p>
                            <?php } ?>
                            <p class="position-relative sub-details-infos toggle-details toggle-details-<?= $params['id'] ?>">
                                <?= $result['result_summary']['phone_plug']?>
                            </p>
                            <p class="position-relative sub-details-infos toggle-details toggle-details-<?= $params['id'] ?>">
                                <?=$result['result_summary']['max_activation_time'];?>
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

