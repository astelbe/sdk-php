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
<div class="row">
<?php
//debug($params);
foreach ($params as $key => $result) {
//	debug(count($result));
////	debug($result['products']);
//	debug($result['products'][$key]['brand_name']);
	
//	if ($key === 0) {
//		debug($result['products'][0]['brand_name']);
//	} elseif ($key === 1) {
//		debug($result['products'][1]['brand_name']);
//	} else {
//		debug($result['products'][0]['brand_name']);
//	}
	?>
	
	
	
	<?php
	foreach ($result['products'] as $key => $item) {
		
		//debug($item);
		
		if ($key === 0) {
			debug($item);
		} else {
			debug('second one');
		}

		?>
		
		<div class="col-md-3 mb-5">
			<div class="px-3 py-4 shadow rounded-lg">
				<div class="py-1 px-2 position-relative rounded-sm" style="color:#fff; background-color: #f23078; left:100px; width: 130px;">
					<?= $result['total_pricings']['total_cashback']?></div>
				<h1>
					<?= $result['products'][0]['brand_name'];?>
				</h1>
				<p>
					<?= $result['products'][0]['short_name'];?>
				</p>
				<?php
				if ($item['mobile'] !== false){
				?>
				<div class="">
					<h6>GSM</h6>
					<li><?= $item['mobile']['included_data_volume']; ?></li>
					<li><?= $item['mobile']['included_sms'] ?></li>
					<li><?= $item['mobile']['included_minutes_calls'] ?></li>
				</div>
				<?php
				} else {
					false;
				}
				if ($item['internet'] !== false){
				?>
				<div class="">
					<h6>Internet</h6>
					<li><?= $item['internet']['bandwidth_download']; ?></li>
					<li><?= $item['internet']['bandwidth_volume']; ?></li>
				</div>
				<?php
				} else {
					false;
				}
				if ($item['tv'] !== false){
				?>
				<div class="">
					<h6>TV</h6>
					<li><?= $item['tv']['number_tv_channel']; ?></li>
					<li><?= $item['tv']['decoder_application']; ?></li>
				</div>
				<?php
				} else {
					false;
				}
				if ($item['fix'] !== false){
						?>
						<div class="">
							<h6>Fix</h6>
							<li><?= $item['mobile']['included_minutes_calls']; ?></li>
						</div>
						<?php
					} else {
						false;
					}
				
				?>
				
				<div class="mt-4">
				<h1>
					<?= $result['products'][1]['brand_name'];?>
				</h1>
				<p>
					<?= $result['products'][1]['short_name'];?>
				</p>
				</div>
				

					<?= $result['total_pricings']['order_url'];?>
			</div>
		</div>
	
	<?php
	}
}
	?>
		</div>
	</div>
<?php
//}
?>
</div>

