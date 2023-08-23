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

?>

<div class="container mt-5">
	<div class="row">
<?php
//debug($params);
foreach ($params as $key => $result) {
//	debug(count($result));
//	debug($result['products']);
//	debug($result['products'][$key]['brand_name']);
	?>

		<div class="col-md-3 mb-5">
			<div class="px-3 py-4 shadow rounded-lg h-100">
				<div class="mt-n5 ml-3 py-2 px-2 position-relative rounded-sm" style="color:#fff; background-color: #f23078; left:100px; width: 130px; font-size: 1.125rem;">
				<?= $result['total_pricings']['total_cashback']?>
				</div>
					<div class="min-vh-100">
	<?php
	foreach ($result['products'] as $key => $item) {
		?>
			<div class="pb-4">
				<h1 class=""><?= $item['brand_name']; ?></h1>
				<p class=""><?= $item['short_name']; ?></p>
				<?php
				if ($item['mobile'] !== false){
				?>
					<p>GSM:</p>
					<li><?= $item['mobile']['included_data_volume']; ?></li>
					<li><?= $item['mobile']['included_sms'] ?></li>
					<li><?= $item['mobile']['included_minutes_calls'] ?></li>
				<?php
				} else {
					false;
				}
				if ($item['internet'] !== false){
				?>
					<p>Internet:</p>
					<li><?= $item['internet']['bandwidth_download']; ?></li>
					<li><?= $item['internet']['bandwidth_volume']; ?></li>

				<?php
				} else {
					false;
				}
				if ($item['tv'] !== false){
				?>
					<p class="mt-3">TV:</p>
					<?php debug($item['tv'])?>
					<li><?= $item['tv']['number_tv_channel']; ?></li>
					<li><?= $item['tv']['decoder_application']; ?></li>

				<?php
				} else {
					false;
				}
				if ($item['fix'] !== false){
						?>

							<li><?= $item['mobile']['included_minutes_calls']; ?></li>

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
						<div>
							<?=$result['total_pricings']['order_url'];?>
				</div>
			</div>
		</div>

<?php
}
?>
	</div>
</div>

