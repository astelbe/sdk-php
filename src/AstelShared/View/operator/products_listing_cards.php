<?php

use CakeUtility\Hash;
//debug($params);
//debug($params['products']);
/*
	This template received $params :	array of results with products and pricings
	$params =	[
		'products' => [
			[
				'brand_name' => 'Proximus',
				'short_name' => 'Tuttimus',
				'...' => ...,
			],
		],
		'total_pricings' => [
				'price' => 99,
				'...' => ...,
		],
	];
*/

$toggle = '<div class="d-flex align-items-center mr-3 text-lighter text-uppercase"
											style="right:0; z-index:999">' . __d('CompareAstelBe', 'switch_resume') . '<input class="mx-2 switch-toggle-details" id="toggle-details-' . $key  . '" type="checkbox">' . __d('CompareAstelBe', 'switch_details') . '</div>';
?>

<style>
	.grid-container {
		display: grid;
		grid-template-columns: repeat(4, auto);
		/*gap: 6px;*/
	}
	
	.grid-container-internet {
		display: grid;
		grid-template-columns: repeat(3, auto);
		/*gap: 6px;*/
	}
	
	.grid-container-tv {
		display: grid;
		grid-template-columns: repeat(3, auto);
		gap: 63px;
	}
	
	.grid-container-fix {
		display: grid;
		grid-template-columns: repeat(3, auto);
		/*gap: 0px;*/
	}
</style>

<div class="container mt-5">
	<?=$toggle;?>
	<div class="row">
	<?php
	foreach ($params as $key => $result) {
	?>
		<div class="col-md-4 mb-5 align-self-lg-start">
<!--		<div class="col-md-4 mb-5">-->
			<div class="px-3 py-4 shadow rounded-lg d-flex h-100 flex-column justify-content-lg-between">
				<div class="mt-n5 ml-3 py-2 px-2 position-relative rounded-sm" style="color:#fff; background-color: #f23078; left: 160px; width: 160px; font-size: 1.125rem;">
				<?= $result['total_pricings']['total_cashback']?>
				</div>
					<div class="" style="">
						<?php
						foreach ($result['products'] as $key => $item) {
//							debug($item);
//							?>
								<div class="pb-4">
									<div style="min-height: 100px;">
										<h1 class=""><?= $item['brand_name']; ?></h1>
										<p class=""><?= $item['short_name']; ?></p>
									</div>
											<?php
											//mobile
											if ($item['mobile'] !== false){
											?>
<!--												<div class="grid-container">-->
												<div class="">
													<div class="label">GSM:</div>
<!--													<div><strong>--><?//= $item['mobile']['included_data_volume']; ?><!--</strong></div>-->
<!--													<div>--><?//= $item['mobile']['included_minutes_calls'] ?><!--</div>-->
<!--													<div>--><?//= $item['mobile']['included_sms'] ?><!--</div>-->
													<div><strong><?= $item['mobile']['included_data_volume']; ?></strong><?= $item['mobile']['included_minutes_calls'] ?><?= $item['mobile']['included_sms'] ?></div>
												</div>
													<?= $item['mobile']['price_description']; ?>
											<?php
											} else {
												false;
											}
											// internet
											if ($item['internet'] !== false){
											?>
												<div class="grid-container-internet">
													<div class="label mt-2">Internet: </div>
													<div class="mt-2"><strong><?=$item['internet']['bandwidth_download']; ?></strong></div>
													<div class="mt-2"><?= $item['internet']['bandwidth_volume']; ?></div>
												</div>
													<?= $item['internet']['price_description']; ?>
											<?php
											} else {
												false;
											}
											// tv
											if ($item['tv'] !== false){
											?>
												<div class="grid-container-tv">
													<div class="label mt-2">TV: </div>
													<div class="mt-2">
														<strong><?= $item['tv']['number_tv_channel']; ?></strong>
													</div>
													<?php if ($item['tv']['decoder_application']) {?>
													<div class="mt-2">
														<?= $item['tv']['decoder_application']; ?>
													</div>
													<?php  } elseif ($item['tv']['decoder_only']) { ?>
													<div class="mt-2">
														<?= $item['tv']['decoder_only']; ?>
													</div>
													<?php  } else  { ?>
													<div>
														<?= $item['tv']['application_only']; ?>
													</div>
													}
												<?php } ?>
												</div>
												<?= $item['tv']['price_description'];?>
											<?php
											} else {
												false;
											}
											// fix
											if ($item['fix'] !== false){
													?>
												<div class="grid-container-fix">
													<div class="label mt-2">FIX:</div>
														<?php if ($item['fix']['included_minutes_calls'] !== null) {?>
													<div class="mt-2">
														<?= $item['fix']['included_minutes_calls']; ?>
													</div class="mt-2">
												</div>
													<?= $item['fix']['price_description'] ?>
													<?php } ?>
													<?php
												} else {
													false;
												}
											?>
								</div>
						<?php
						}
					?>
					</div>
					<div class="results-wrapper">
						<div class="results-price">
							<?=$result['total_pricings']['quality_score'];?>
								<div class="">
									<?=$result['total_pricings']['total_price'];?>
									<?=$result['total_pricings']['total_price_without_discount'];?>
								</div>
								<div class="setup-wrapper">
									<p>
									<p class="mb-n1">Activation et Installation</p>
									<?=$result['total_pricings']['reduced_total_setup_price'];?>
									<?=$result['total_pricings']['full_total_setup_price'];?>
									</p>
								</div>
								<?=$result['total_pricings']['products_total_savings']?>
								<div class="pt-4 mt-1">
									<?=$result['total_pricings']['order_url'];?>
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

