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

<div class="container mt-5">
	<?=$toggle;?>
	<div class="row">
	<?php
	foreach ($params as $key => $result) {
	?>
		<div class="col-md-3 mb-5">
			<div class="px-3 py-4 shadow rounded-lg h-100 d-flex flex-column justify-content-lg-between">
				<div class="mt-n5 ml-3 py-2 px-2 position-relative rounded-sm" style="color:#fff; background-color: #f23078; left:100px; width: 130px; font-size: 1.125rem;">
				<?= $result['total_pricings']['total_cashback']?>
				</div>
					<div class="">
						<?php
						foreach ($result['products'] as $key => $item) {
//							debug($item);
							?>
								<div class="pb-4">
									<h1 class=""><?= $item['brand_name']; ?></h1>
										<p class=""><?= $item['short_name']; ?></p>
											<?php
											//mobile
											if ($item['mobile'] !== false){
											?>
												<p>GSM:</p>
												<li><?= $item['mobile']['included_data_volume']; ?></li>
												<li><?= $item['mobile']['included_sms'] ?></li>
												<li><?= $item['mobile']['included_minutes_calls'] ?></li>
												<?= $item['mobile']['price_description'] ?>
											<?php
											} else {
												false;
											}
											// internet
											if ($item['internet'] !== false){
											?>
												<p>Internet:</p>
												<li><?= $item['internet']['bandwidth_download']; ?></li>
												<li><?= $item['internet']['bandwidth_volume']; ?></li>
												<?= $item['internet']['price_description'] ?>
											<?php
											} else {
												false;
											}
											// tv
											if ($item['tv'] !== false){
											?>
												<p class="mt-3">TV:</p>
<!--												--><?php //debug($item['tv'])?>
												<li><?= $item['tv']['number_tv_channel']; ?></li>
												<?php if ($item['tv']['decoder_application']) {?>
												<li><?= $item['tv']['decoder_application']; ?></li>
												<?php  } elseif ($item['tv']['decoder_only']) { ?>
												<li><?= $item['tv']['decoder_only']; ?></li>
												<?php  } else  { ?>
												<li><?= $item['tv']['application_only']; ?></li>
												<?= $item['tv']['price_description'] ?>
											<?php
												}
											} else {
												false;
											}
											// fix
											if ($item['fix'] !== false){
													?>
													<p class="mt-3">FIX:</p>
												<?php if ($item['fix']['included_minutes_calls'] !== null) {?>
													<li><?= $item['fix']['included_minutes_calls']; ?></li>
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
								<div>
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

