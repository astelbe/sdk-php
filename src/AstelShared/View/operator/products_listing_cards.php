<?php

use CakeUtility\Hash;
debug($params);
?>
<!--<div class="row">-->
<!--	--><?php
//	foreach ($params['products'] as $product) {
//		//debug($product);
//		?>
<!--	<div class="col-md-3">-->
<!--	<span>-->
<!--		--><?php //if (Hash::get($product, 'products', false)) {
//			foreach ($product['products'] as $productId => $productData) {
//				echo "<h2>{$productData['name']['FR']}</h2>";
//				//debug($productData);
//			}
//		}
//		?>
<!--	</span>-->
<!--	<span>-->
<!--    --><?php
//		if (Hash::get($product, 'total_pricings.total_price', false)) {
//			$totalPrice = $product['total_pricings']['total_price'];
//			echo "<div>TOTAL : {$totalPrice} pendant XX mois</div>";
//			//debug($product);
//		}
//		?>
<!--	</span>-->
<!--	<span>-->
<!--    --><?php
//		if (Hash::get($product, 'total_pricings.total_price_without_discount', false)) {
//			$totalPriceWithoutDiscount = $product['total_pricings']['total_price_without_discount'];
//			echo "<div>puis {$totalPriceWithoutDiscount} /mois TVAC</div>";
//		}
//		?>
<!--	</span>-->
<!--	</div>-->
<!--	-->
<!--	--><?php //} ?>
<!--</div>-->

<!--<h3>PRODUCT CARDS</h3>-->
<!--<div class="row">-->
<!--	--><?php //foreach ($params['products'] as $product) : ?>
<!--		<div class="col-md-3">-->
<!--			<div class="card mb-3">-->
<!--				<div class="card-body">-->
<!--					--><?php
//					foreach ($product['products'] as $mobileProducts => $mobile) {
//						$includedDataVolume = $mobile['play_description']['mobile']['included_data_volume'];
//						$includedDataVolumeFormatted = round($includedDataVolume / 1024, 2);
//						echo "<h2>{$mobile['brand_name']}</h2>";
//						echo "<h2>GSM: {$includedDataVolumeFormatted} GB</h2>";
//					}?>
<!--					-->
<!--					<h1>+</h1>-->
<!--					-->
<!--						--><?php
//						foreach ($product['products'] as $productId => $productData) {
//							echo "<h2>{$productData['brand_name']}</h2>";
//							echo "<h3>Internet: {$productData['play_description']['internet']['bandwidth_volume']}</h3>";
//							echo "<h3>TV: {$productData['play_description']['tv']['number_tv_channel']} chaînes</h3>";
//						}
//						?>
<!--					-->
<!--					<div>TOTAL: --><?//= $product['total_pricings']['total_price'] ?><!-- pendant XX mois</div>-->
<!--					<div>puis --><?//= $product['total_pricings']['total_price_without_discount'] ?><!-- /mois TVAC</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		</div>-->
<!--	--><?php //endforeach; ?>
<!--</div>-->

<h3>PRODUCT CARDS</h3>
<div class="row">
	<?php foreach ($params['products'] as $product) : ?>
		<div class="col-md-3">
			<div class="card mb-3">
				<div class="card-body">
					<?php
					$mobileBrandName = '';
					foreach ($product['products'] as $mobileProducts => $mobile) {
						
						echo "<h2 class='float-right py-1 px-3' style='color:#fff; background-color: #f23078;'>€ {$mobile['commission']['cashback_amount']} cashback</h2><br>";
						
						$includedDataVolume = $mobile['play_description']['mobile']['included_data_volume'];
						$includedDataVolumeFormatted = round($includedDataVolume / 1024, 2);
						if ($mobileBrandName !== $mobile['brand_name']) {
							echo "<h2 class='mt-4'>{$mobile['brand_name']}</h2>";
							$mobileBrandName = $mobile['brand_name'];
						}
						echo "<h2>GSM: {$includedDataVolumeFormatted} GB</h2>";
					}
					?>
					<h1>+</h1>
					<?php
					$mobileBrandName = ''; // Initialize the variable outside the loop
					foreach ($product['products'] as $productId => $productData) {
						if ($mobileBrandName !== $productData['brand_name']) {
							echo "<h2>{$productData['brand_name']}</h2>";
							$mobileBrandName = $productData['brand_name'];
						}
						if ($productData['is_internet']) {
							echo "<h3>Internet: {$productData['play_description']['internet']['bandwidth_volume']}</h3>";
						}
						if ($productData['is_tv']) {
							echo "<h3>TV: {$productData['play_description']['tv']['number_tv_channel']} chaînes</h3>";
						}
					}
					?>
					<div>TOTAL: <?= $product['total_pricings']['total_price'] ?> pendant XX mois</div>
					<div>puis <?= $product['total_pricings']['total_price_without_discount'] ?> /mois TVAC</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>







