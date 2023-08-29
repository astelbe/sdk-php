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
	/*.label {*/
	/*	font-size: 0.875rem;*/
	/*}*/
	
	/*tr {*/
	/*	vertical-align: top;*/
	/*}*/
</style>

<div class="container mt-5">
	<?=$toggle;?>
	<div class="row mt-4">
	<?php
	foreach ($params as $key => $result) {
	?>
<!--		<div class="col-md-4 mb-5 align-self-lg-start">-->
		<div class="col-md-4 mb-5">
				<div class="mt-n3 ml-3 py-2 pl-4 position-absolute rounded-sm" style="color:#fff; background-color: #f23078; left: 190px; width: 160px; font-size: 1.125rem;">
				<?= $result['total_pricings']['total_cashback']?>
				</div>
			<div class="px-3 py-4 shadow rounded-lg d-flex h-100 flex-column justify-content-between">
<!--			<div class="px-3 py-4 shadow rounded-lg h-100">-->
					<div class="mt-2">
						<?php
						foreach ($result['products'] as $key => $item) {
//							debug($item['mobile']);
//							?>
								<div class="pt-1 pb-3 pl-2 pr-1 mb-2 rounded" style="background-color: #f5f5f5">
									<div>
										<h2 class="" style="font-size:1.1rem"><?= $item['brand_name']; ?></h2>
										<p class="text-uppercase" style="font-size: 1.1rem; color: #f23078"><?= $item['short_name']; ?></p>
									</div>
									<table>
											<?php
											//mobile
											if ($item['mobile'] !== false){
											?>
												<div class="d-flex align-items-baseline" style="line-height:30px">
													<div class="mr-2">
														<span style="width:30px">GSM</span>
													</div>
													<div>
														<span>
													<strong style="font-size: 1.25rem; color:#1f438c!important;"><?= $item['mobile']['included_data_volume']; ?></strong>
														</span>
														<span class="mr-1">
																<?= $item['mobile']['included_minutes_calls'] ?>
														</span>
														<span class="mr-1">
																<?= $item['mobile']['included_sms'] ?>
														</span>
													</div>
												</div>
													<?= $item['mobile']['price_description']; ?>
											<?php
											} else {
												false;
											}
											// internet
											if ($item['internet'] !== false){
											?>
													<div class="d-flex align-items-baseline" style="line-height:30px">
															<div class="mr-2">
																<span style="width:30px">NET</span>
															</div>
														<div>
																<span>Vitesse <strong class="mt-n1" style="font-size: 1.25rem; color:#1f438c!important;"><?= $item['internet']['bandwidth_download']; ?></strong>
																	<span class="mr-1"><?= $item['internet']['bandwidth_volume']; ?></span>
																</span>
														</div>
													</div>
													
													<?= $item['internet']['price_description']; ?>
											<?php
											} else {
												false;
											}
											// tv
											if ($item['tv'] !== false){
											?>
											<div class="d-flex align-items-baseline" style="line-height:30px">
												<div class="mr-3">
													<span class="label d-inline-flex">TV:</span>
												</div>
												<div>
															<span class="mr-1" style="font-size: 1.25rem; color:#1f438c!important;">
																<strong><?= $item['tv']['number_tv_channel']; ?></strong>
															</span>
													<?php if ($item['tv']['decoder_application']) {?>
														<span class="mr-1">
																<?= $item['tv']['decoder_application']; ?>
															</span>
													<?php  } elseif ($item['tv']['decoder_only']) { ?>
														<span class="mr-1">
																<?= $item['tv']['decoder_only']; ?>
															</span>
													<?php  } else  { ?>
														<div>
																<span class="mr-1">
																<?= $item['tv']['application_only']; ?>
																</span>
														</div>
														}
													<?php } ?>
												</div>
										</div>
										<?= $item['tv']['price_description']; ?>
									
										
										<?php
											} else {
												false;
											}
										
										
							
											// fix
											if ($item['fix'] !== false){
													?>
														<div class="d-flex align-items-baseline" style="line-height:30px">
															<div class="mr-3">
																<span class="label">FIX</span>
															</div>
															<div>
																<?php if ($item['fix']['included_minutes_calls'] !== null) { ?>
																	<span class="mr-1"><?= $item['fix']['included_minutes_calls']; ?></span>
																<?php } ?>
															</div>
														</div>
													<?= $item['fix']['price_description'] ?>
													<?php } else {
														false;
													}
											?>
										</table>
								</div>
						<?php
						}
					?>
					</div>
					<div class="results-wrapper text-center">
						<div class="results-price d-flex flex-column justify-content-center">
							<div class="">
								<?=$result['total_pricings']['quality_score'];?>
							</div>
								<div class="">
									<?=$result['total_pricings']['total_price'];?>
									<?=$result['total_pricings']['total_price_without_discount'];?>
								</div>
								<div class="setup-wrapper">
									<p>
									<p class="mb-n1 font-weight-bold" style="font-size: 1.2rem">Activation et Installation</p>
									<?=$result['total_pricings']['reduced_total_setup_price'];?>
									<?=$result['total_pricings']['full_total_setup_price'];?>
									</p>
								</div>
								<?=$result['total_pricings']['products_total_savings']?>
								<div class="pt-4 mt-5" style="margin-top: 120px;">
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

