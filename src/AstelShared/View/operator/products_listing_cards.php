<?php

use CakeUtility\Hash;
debug($params);



?>

<h2>PRODUCT CARDS</h2>
<div class="col-md-4">
	<div class="row h-100">
<!--		  --><?php
//        foreach ($params['products'] as $product) {
//					//debug($product['products']);
//					debug($product['total_pricings']['quality_score']);
//					//debug($product['name']);
//			// Display the product name
//
//			 echo $product['total_pricings']['quality_score'];
//			 echo $product['total_pricings']['total_price'];
//
//        }
//
//		?>
	</div>
</div>


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

<h3>PRODUCT CARDS VERSION 2</h3>
<div class="row">
	<?php foreach ($params['products'] as $product) : ?>
		<div class="col-md-3">
			<div class="card">
				<div class="card-body">
						<?php
						foreach ($product['products'] as $productId => $productData) {
							echo "<h2>{$productData['name']['FR']}</h2>";
						}
						?>
					<div>TOTAL: <?= $product['total_pricings']['total_price'] ?> pendant XX mois</div>
					<div>puis <?= $product['total_pricings']['total_price_without_discount'] ?> /mois TVAC</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>





