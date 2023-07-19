<?php

use CakeUtility\Hash;
debug($params);

function getFirst(array &$array) {
	$reversed = array_reverse($array);
	
	return array_pop($reversed);
}

?>
<h3>PRODUCT CARDS</h3>
<div class="row">
	<?php foreach ($params['products'] as $product) : ?>
		<div class="col-md-3">
			<div class="card mb-3">
				<div class="card-body">
					<?php
					$mobileBrandName = '';
					$cashbackTotal = 0; // Initialize cashback total
					foreach ($product['products'] as $mobileProducts => $mobile) {
						
						$nbrProducts = $product['total_pricings']['number_products'];
						$nbrProviders = $product['total_pricings']['number_providers'];
						
						$displayedData['product'] = $product;
						$displayedData = [];
						$i = 1;
						
						$productMobileIDs = $mobile['id'];
						
						$displayedData['block' . $i] = [];
						$displayedData['block' . $i]['lenght'] = 1;
						$displayedData['block' . $i]['productIDs'] = $productMobileIDs;
						$displayedData['block' . $i]['nbrProducts'] = !empty($mobile['play_description']['mobile']) ? count($mobile['play_description']['mobile']) : -1;
						debug($displayedData['block' . $i]['nbrProducts']);
						
//						$productTypeM = $mobile['play_description']['mobile'];
//						debug($productTypeM);
//
//						$productTypeI = $mobile['play_description']['internet'];
//						debug($productTypeI);
//
//						$productTypeF = $mobile['play_description']['fix'];
//						debug($productTypeF);
//
//						$productTypeTv = $mobile['play_description']['tv'];
//						debug($productTypeTv);
					
						
						$includedMinutes = $mobile['play_description']['mobile']['included_minutes_calls'];
						$includedDataVolume = $mobile['play_description']['mobile']['included_data_volume'];
						$includedDataVolumeFormatted = round($includedDataVolume / 1000, 1);
						$cashbackTotal = $product['total_pricings']['total_cashback'];
						debug($cashbackTotal);
						echo "<h2 class='float-right py-1 px-3' style='color:#fff; background-color: #f23078;'>€ " . $cashbackTotal . " cashback</h2><br>";
						
						if ($includedDataVolumeFormatted !== 0 && $includedDataVolumeFormatted !== null && $includedDataVolumeFormatted !== '') {
							
							// Add cashback amount to the total
//							$cashbackTotal += $mobile['commission']['cashback_amount'];
//							$cashbackTotal += $mobile['total_pricings']['total_cashback'];
							
							// Output the merged cashback
//							echo "<h2>Merged Cashback: € {$cashbackTotal}</h2>";
						
							// If I have more than one cashbacks , I need to merge them , so I output just one
//							echo "<h2 class='float-right py-1 px-3' style='color:#fff; background-color: #f23078;'>€ " . $cashbackTotal . " cashback</h2><br>";
							
							echo "<div class='border border-info p-3'>";
							
							// Output only if it's the first occurrence of the brand
							if ($mobileBrandName !== $mobile['brand_name']) {
								// if included data equals 0 , not show the brand name
								if ($includedDataVolumeFormatted !== 0) {
									echo "<h2 class='mt-4'>{$mobile['brand_name']}</h2>";
									$mobileBrandName = $mobile['brand_name'];
								}
							}
							
							echo "<div>GSM: " . __d('CoreAstelBe', '%s GB', [$includedDataVolumeFormatted]) . "</div>";
							
							echo "<div>" . __d('CompareAstelBe', 'Included Minutes: %s', $mobile['play_description']['mobile']['included_minutes_calls']) . "</div>";
							echo "<div>" . __d('CompareAstelBe', 'Included SMS: %s', $mobile['play_description']['mobile']['included_sms']) . "</div>";
							echo "</div>";
						}
					}
					?>
					
					<div class="d-flex justify-content-center mt-2">
						<h1>+</h1>
					</div>
					
					<?php
					$mobileBrandName = ''; // Initialize the variable outside the loop
					foreach ($product['products'] as $productId => $productData) {
						
						if ($includedDataVolumeFormatted !== 0 && $includedDataVolumeFormatted !== null && $includedDataVolumeFormatted !== '') {
							
							echo "<div class='border border-info p-3'>";
							
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
							
							echo "</div>";
						}
					}
					?>
					
					<div class="d-flex justify-content-center mt-2">
						<h1>=</h1>
					</div>
					
					<div>TOTAL: <?= $product['total_pricings']['total_price'] ?> pendant XX mois</div>
					<div>puis <?= $product['total_pricings']['total_price_without_discount'] ?> /mois TVAC</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>







