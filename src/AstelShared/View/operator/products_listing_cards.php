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
		<div class="col-md-4 mb-5 align-self-start">
<!--		<div class="col-md-4 mb-5">-->
			<div class="px-3 py-4 shadow rounded-lg d-flex h-100 flex-column justify-content-lg-between">
				<div class="mt-n5 ml-3 py-2 px-2 position-relative rounded-sm" style="color:#fff; background-color: #f23078; left: 160px; width: 160px; font-size: 1.125rem;">
				<?= $result['total_pricings']['total_cashback']?>
				</div>
					<div class="">
						<?php
						foreach ($result['products'] as $key => $item) {
//							debug($item);
//							?>
								<div class="pb-4">
									<div>
										<h2 class="" style="font-size:2rem"><?= $item['brand_name']; ?></h2>
										<p class="text-uppercase"><?= $item['short_name']; ?></p>
									</div>
											<?php
											//mobile
											if ($item['mobile'] !== false){
											?>
												<table>
													<colgroup>
														<col style="width: 20%;">
														<col style="width: 80%;">
													</colgroup>
													<tr>
														<td class="label">GSM:</td>
														<td>
													<span class="mr-1 font-weight-bolder" style="font-size: 1.25rem;">
															<strong>
																	<?= $item['mobile']['included_data_volume']; ?>
															</strong>
													</span>
																						<span class="mr-1">
															<?= $item['mobile']['included_minutes_calls'] ?>
													</span>
																						<span class="mr-1">
															<?= $item['mobile']['included_sms'] ?>
													</span>
																					</td>
																				</tr>
																			</table>
												<div class="">
													<span class="label">GSM:</span>
													<span class="mr-1 font-weight-bolder" style="font-size: 1.25rem;">
														<strong>
															<?= $item['mobile']['included_data_volume']; ?>
														</strong>
													</span>
													<span class="mr-1">
														<?= $item['mobile']['included_minutes_calls'] ?>
													</span>
													<span class="mr-1">
														<?= $item['mobile']['included_sms'] ?>
													</span>
												</div>
													<?= $item['mobile']['price_description']; ?>
											<?php
											} else {
												false;
											}
											// internet
											if ($item['internet'] !== false){
											?>
												<div class="mt-1">
													<span class="label">Internet: </span>
													<span class="mr-1">Vitesse  <strong style="font-size: 1.25rem;"><?=$item['internet']['bandwidth_download']; ?></strong></span>
													<span class="mr-1"><?= $item['internet']['bandwidth_volume']; ?></span>
												</div>
													<?= $item['internet']['price_description']; ?>
											<?php
											} else {
												false;
											}
											// tv
											if ($item['tv'] !== false){
											?>
												<div class="mt-2">
													<span class="label">TV: </span>
													<span class="mr-1">
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
												<?= $item['tv']['price_description'];?>
											<?php
											} else {
												false;
											}
											// fix
											if ($item['fix'] !== false){
													?>
												<div class="mt-2">
													<span class="label">FIX:</span>
														<?php if ($item['fix']['included_minutes_calls'] !== null) {?>
													<span class="mr-1">
														<?= $item['fix']['included_minutes_calls']; ?>
													</span class="mr-1">
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
									<p class="mb-n1" style="font-size: 1.2rem">Activation et Installation</p>
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

