<?php

use CakeUtility\Hash;
//debug($params);
//debug($params['products']);

?>
<div class="row">
	<div>
<?php
foreach ($params['products'] as $product) {
	$brandName = $product['brand_name'];
	$shortName = $product['short_name'];
	$totalCashback = $product['total_cashback'];
	$totalPrice = $product['total_price'];
//	debug($brandName);
	?>
<!--	<div class="col-md-3">-->
<!--		<div>-->
	<?php
	echo $brandName;
	echo $shortName;
	echo $totalCashback;
	echo $totalPrice;
	?>
<!--		</div>-->
<!--	</div>-->
<?php
}
?>
	</div>
</div>

<!--<h3>PRODUCT CARDS</h3>-->
<!--<div class="row">-->
<!--	--><?php //foreach ($params['products'] as $product) : ?>
<!--		<div class="col-md-3">-->
<!--			<div class="card mb-3">-->
<!--				<div class="card-body">-->
<!--					--><?php
//					$mobileBrandName = '';
//					$cashbackTotal = 0; // Initialize cashback total
//					foreach ($product['products'] as $mobileProducts => $mobile) {
//						$includedMinutes = $mobile['play_description']['mobile']['included_minutes_calls'];
//						$includedDataVolume = $mobile['play_description']['mobile']['included_data_volume'];
//						$includedDataVolumeFormatted = round($includedDataVolume / 1000, 1);
//
//						if ($includedDataVolumeFormatted !== 0 && $includedDataVolumeFormatted !== null && $includedDataVolumeFormatted !== '') {
//
//							// Add cashback amount to the total
//							$cashbackTotal += $mobile['commission']['cashback_amount'];
//
//							// Output the merged cashback
//							//							echo "<h2>Merged Cashback: € {$cashbackTotal}</h2>";
//
//							// If I have more than one cashbacks , I need to merge them , so I output just one
//							echo "<h2 class='float-right py-1 px-3' style='color:#fff; background-color: #f23078;'>€ " . $cashbackTotal . " cashback</h2><br>";
//
//							echo "<div class='border border-info p-3'>";
//
//							// Output only if it's the first occurrence of the brand
//							if ($mobileBrandName !== $mobile['brand_name']) {
//								// if included data equals 0 , not show the brand name
//								if ($includedDataVolumeFormatted !== 0) {
//									echo "<h2 class='mt-4'>{$mobile['brand_name']}</h2>";
//									$mobileBrandName = $mobile['brand_name'];
//								}
//							}
//
//							echo "<div>GSM: " . __d('CoreAstelBe', '%s GB', [$includedDataVolumeFormatted]) . "</div>";
//
//							echo "<div>" . __d('CompareAstelBe', 'Included Minutes: %s', $mobile['play_description']['mobile']['included_minutes_calls']) . "</div>";
//							echo "<div>" . __d('CompareAstelBe', 'Included SMS: %s', $mobile['play_description']['mobile']['included_sms']) . "</div>";
//							echo "</div>";
//						}
//					}
//					?>
<!--					-->
<!--					<div class="d-flex justify-content-center mt-2">-->
<!--						<h1>+</h1>-->
<!--					</div>-->
<!--					-->
<!--					--><?php
//					$mobileBrandName = ''; // Initialize the variable outside the loop
//					foreach ($product['products'] as $productId => $productData) {
//
//						if ($includedDataVolumeFormatted !== 0 && $includedDataVolumeFormatted !== null && $includedDataVolumeFormatted !== '') {
//
//							echo "<div class='border border-info p-3'>";
//
//							if ($mobileBrandName !== $productData['brand_name']) {
//								echo "<h2>{$productData['brand_name']}</h2>";
//								$mobileBrandName = $productData['brand_name'];
//							}
//							if ($productData['is_internet']) {
//								echo "<h3>Internet: {$productData['play_description']['internet']['bandwidth_volume']}</h3>";
//							}
//							if ($productData['is_tv']) {
//								echo "<h3>TV: {$productData['play_description']['tv']['number_tv_channel']} chaînes</h3>";
//							}
//
//							echo "</div>";
//						}
//					}
//					?>
<!--					-->
<!--					<div class="d-flex justify-content-center mt-2">-->
<!--						<h1>=</h1>-->
<!--					</div>-->
<!--					-->
<!--					<div>TOTAL: --><?//= $product['total_pricings']['total_price'] ?><!-- pendant XX mois</div>-->
<!--					<div>puis --><?//= $product['total_pricings']['total_price_without_discount'] ?><!-- /mois TVAC</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		</div>-->
<!--	--><?php //endforeach; ?>
<!--</div>-->

<?php
//use CakeUtility\Hash;
//
////debug($params);
//function getFirst(array &$array) {
//	$reversed = array_reverse($array);
//	
//	return array_pop($reversed);
//}
//
//// Handle responsive type to load. If no value $userWindowWidth, it's set to screen
////$responsiveLoadType = $this->ComparatorUtils->getResponsiveLoadType($userWindowWidth);
//?>
<!--<h3>PRODUCT CARDS</h3>-->
<!--<div class="row">-->
<!--	--><?php
//	$lastBrandName = null;
//	foreach ($params['products'] as $product) : ?>
<!--	<div class="col-md-12">-->
<!--		4cols-->
<!--	<div class="col-md-3">-->
<!--			2cols-->
<!--	<div class="col-md-6">-->
<!--			3cols-->
<!--		<div class="col-md-4">-->
<!--			<div class="card mb-3">-->
<!--				<div class="card-body">-->
<!--					--><?php
//					$index = 1;
//					
//					$mobileBrandName = '';
//					$cashbackTotal = 0; // Initialize cashback total
//					
//					foreach ($product['products'] as $playProducts => $play) {
//						// Limit play results at 10 to increase performances
//						if ($index >= 11) {
//							break;
//						}
//						
//						$brandName = $play['brand_name'];
//						
//						// Skip if the brand name is -1 or empty
//						if ($brandName === -1 || empty($brandName)) {
//							continue;
//						}
//						
//						// Count the number of products for each brand
//						$brandCounts[$brandName] = isset($brandCounts[$brandName]) ? $brandCounts[$brandName] + 1 : 1;
//						
////						debug($brandCounts);
//						
//						$nbrProducts = $product['total_pricings']['number_products'];
//						$nbrProviders = $product['total_pricings']['number_providers'];
//						
//						$displayedData['product'] = $product;
//						$displayedData = [];
//						$i = 1;
//						
//						
//						
//					
//						
//						//$productMobileIDs = $play['id'];
//						//debug($product);
//						$productMobileIDs = Hash::combine($product, 'products.{n}.id', 'products.{n}.id');
//						$displayedData['block' . $i] = [];
//						$displayedData['block' . $i]['lenght'] = 1;
//						$displayedData['block' . $i]['products']['id'][] = $productMobileIDs;
////						debug($productMobileIDs);
//						$displayedData['block' . $i]['nbrProducts'] = !empty($play['play_description']['play']) ? count($play['play_description']['play']) : -1;
//						$displayedData['block' . $i]['type'] = 'M';
//						$displayedData['block' . $i]['type_name'] = __d('CompareAstelBe', 'mobile');
//						
//						$productInternetIDs = Hash::combine($product, 'products.{n}.id', 'products.{n}.id');
//						$productInternetID = getFirst($productInternetIDs);
//						$internetCountProduct = !empty($play['play_description']['internet']) ? count($play['play_description']['internet']) : -1;
//						if ($internetCountProduct > 0 && in_array($productInternetID, $displayedData['block' . $i]['productIDs'], false)) {
//							$displayedData['block' . $i]['lenght'] += 1;
//						} else {
//							$i++;
//							$displayedData['block' . $i] = [];
//							$displayedData['block' . $i]['lenght'] = 1;
//							$displayedData['block' . $i]['products']['id'][] = 	$productInternetIDs;
////							debug($productInternetIDs);
//							$displayedData['block' . $i]['nbrProducts'] = $internetCountProduct;
//							$displayedData['block' . $i]['type'] = 'I';
//							$displayedData['block' . $i]['type_name'] = __d('CompareAstelBe', 'internet');
//						}
//						
//						$productFixIDs = Hash::combine($product, 'products.{n}.id', 'products.{n}.id');
//						$productFixID = getFirst($productFixIDs);
////						debug($productFixID);
//						$fixCountProduct = !empty($productTypeF) ? count($productTypeF) : -1;
//						if ($fixCountProduct > 0 && in_array($productFixID, $displayedData['block' . $i]['productIDs'], false)) {
//							$displayedData['block' . $i]['lenght'] += 1;
//						} else {
//							$i++;
//							$displayedData['block' . $i] = [];
//							$displayedData['block' . $i]['lenght'] = 1;
//							$displayedData['block' . $i]['products']['id'][] = 	$productFixIDs;
//							$displayedData['block' . $i]['nbrProducts'] = $fixCountProduct;
//							$displayedData['block' . $i]['type'] = 'F';
//							$displayedData['block' . $i]['type_name'] = __d('CompareAstelBe', 'fix');
//							$displayedData['block' . $i]['product'] = $product;
//						}
//						
//						$productTvIDs = Hash::combine($product, 'products.{n}.id', 'products.{n}.id');
//						$productTvID = getFirst($productTvIDs);
//						$tvCountProduct = !empty($productTypeTv) ? count($productTypeTv) : -1;
//						if ($tvCountProduct > 0 && in_array($productTvID, $displayedData['block' . $i]['productIDs'], false)) {
//							$displayedData['block' . $i]['lenght'] += 1;
//						} else {
//							$i++;
//							$displayedData['block' . $i] = [];
//							$displayedData['block' . $i]['lenght'] = 1;
//							$displayedData['block' . $i]['products']['id'][]= $productTvIDs;
//							$displayedData['block' . $i]['nbrProducts'] = $tvCountProduct;
//							$displayedData['block' . $i]['type'] = 'T';
//							$displayedData['block' . $i]['type_name'] = __d('CompareAstelBe', 'television');
//							$displayedData['block' . $i]['product'] = $product;
//						}
//						
//						// If there are two blocks for the same product, merge them
//						$x = 1;
//						while ($x <= count($displayedData)) {
//							$y = $x + 1;
//							while ($y <= count($displayedData)) {
//								if ($displayedData['block' . $x]['nbrProducts'] != -1) {
//									$displayedData['block' . $x]['lenght'] = $displayedData['block' . $x]['lenght'] + $displayedData['block' . $y]['lenght'];
//
//									// Retrieve the cashback total
//									$cashbackTotal = $product['total_pricings']['total_cashback'];
//									echo "<h2 class='float-right py-1 px-3 ml-auto' style='color:#fff; background-color: #f23078;'>€ " . $cashbackTotal . " cashback</h2><br>";
//									
//									$productBrand = Hash::extract($product, 'products.{n}.brand_name');
////									debug($productBrand);
//									
//									
//									echo "<h2 class='mt-4'>";
//									foreach ($productBrand as $brand) {
//										echo '<div>' . $brand . '</div>';
//									}
//									echo "</h2>";
//
//									unset($displayedData['block' . $y]);
//								}
//								$y++;
//							}
//							$x++;
//						}
//						
//						foreach ($displayedData as $blockName => $block) {
//							
//							// if brand name length is duplicated display only once the brand name
//							$productId = $block['products']['id'];
//							
////							if (in_array($productId, $displayedProductIds)) {
////								continue; // Skip if product ID already displayed
////							}
//////
//
////							echo "<h2 class='mt-4'>{$brandName}</h2>";
////							echo $block['type_name'];
//							
//							// Add the product ID to the displayedProductIds array
////							$displayedProductIds[] = $productId;
////							debug($block['products']['id']);
//						
//						}
//						
//							echo "<h2>Total : " . $product['total_pricings']['total_price'] . "</h2>";
//					
////						<!-- block div wrapper that contain the combinations -->
////						echo "<h2 class='mt-4'>{$brandName}</h2>";
////						echo $displayedData['block' . $x]['type_name'];
////						echo '<pre>';
////						print_r($displayedData);
////						echo '</pre>';
////						<!-- end row result -->
//
//						$index++;
//					}
//					
//					//debug($displayedData);
//					
//					// Loop through the brand counts to display the brand name if the conditions are met
////					debug($displayedData);
////					$displayedData = Hash::extract($displayedData, '{n}[nbrProducts > 0]');
////					foreach ($displayedData as $blockName => $block) {
////						if ($block['nbrProducts'] !== -1) {
////
////							debug($displayedData);
////
//////							$typeName = Hash::extract($displayedData, '{n}.type_name');
//////							debug($typeName);
//////							debug($block);
////
////							$countPThisBlock = count($play['id']);
//////							debug($countPThisBlock);
////
////							foreach ($block['products']['id'] as $productID) {
////
////
//////								$p = Hash::get($block, $productID);
//////								debug((int)$block['type_name']['Internet']);
////
//////								debug($productID);
////								//debug($displayedData);
////							}
////
////							echo "<h2 class='mt-4'>{$play['brand_name']}</h2>";
////							echo '<pre>';
////							print_r($displayedData);
////							echo '</pre>';
////						}
////					}
//					?>
<!--				</div>-->
<!--			</div>-->
<!--		</div>-->
<!--	--><?php //endforeach; ?>
<!--</div>-->







