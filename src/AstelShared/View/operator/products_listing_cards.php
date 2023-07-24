<?php
use CakeUtility\Hash;

//debug($params);
function getFirst(array &$array) {
	$reversed = array_reverse($array);
	
	return array_pop($reversed);
}

// Handle responsive type to load. If no value $userWindowWidth, it's set to screen
//$responsiveLoadType = $this->ComparatorUtils->getResponsiveLoadType($userWindowWidth);
?>
<h3>PRODUCT CARDS</h3>
<div class="row">
	<?php foreach ($params['products'] as $product) : ?>
<!--	<div class="col-md-12">-->
<!--		<div class="col-md-3"> 4cols-->
		<!--		2cols-->
<!--		<div class="col-md-6">-->
		<!--	3cols-->
		<div class="col-md-4">
			<div class="card mb-3">
				<div class="card-body">
					<?php
					$index = 1;
					
					$mobileBrandName = '';
					$cashbackTotal = 0; // Initialize cashback total
					
					foreach ($product['products'] as $playProducts => $play) {
						// Limit play results at 10 to increase performances
						if ($index >= 11) {
							break;
						}
						
						$nbrProducts = $product['total_pricings']['number_products'];
						$nbrProviders = $product['total_pricings']['number_providers'];
						
						$displayedData['product'] = $product;
						$displayedData = [];
						$i = 1;
						
						//$productMobileIDs = $play['id'];
						
						$productMobileIDs = Hash::combine($product, 'product_M.{n}.product_id', 'product_M.{n}.product_id');
						$displayedData['block' . $i] = [];
						$displayedData['block' . $i]['lenght'] = 1;
						$displayedData['block' . $i]['productIDs'] = $productMobileIDs;
						$displayedData['block' . $i]['nbrProducts'] = !empty($play['play_description']['play']) ? count($play['play_description']['play']) : -1;
						$displayedData['block' . $i]['type'] = 'M';
						$displayedData['block' . $i]['type_name'] = __d('CompareAstelBe', 'play');
						
						$productInternetIDs = Hash::combine($product, 'product_I.{n}.product_id', 'product_I.{n}.product_id');
						//$productTypeI = $play['play_description']['internet'];
						$productInternetID = getFirst($productInternetIDs);
						$internetCountProduct = !empty($play['play_description']['internet']) ? count($play['play_description']['internet']) : -1;
						if ($internetCountProduct > 0 && in_array(	$productInternetID, $displayedData['block' . $i]['productIDs'], false)) {
							$displayedData['block' . $i]['lenght'] += 1;
						} else {
							$i++;
							$displayedData['block' . $i] = [];
							$displayedData['block' . $i]['lenght'] = 1;
//							$displayedData['block' . $i]['productIDs'] = $productTypeI;
							$displayedData['block' . $i]['productIDs'] = 	$productInternetIDs;
							$displayedData['block' . $i]['nbrProducts'] = $internetCountProduct;
							$displayedData['block' . $i]['type'] = 'I';
							$displayedData['block' . $i]['type_name'] = __d('CompareAstelBe', 'internet');
						}
						
						$productFixIDs = Hash::combine($product, 'product_F.{n}.product_id', 'product_F.{n}.product_id');
//						$productTypeF = $play['play_description']['fix'];
						$productFixID = getFirst($productFixIDs);
						$fixCountProduct = !empty($productTypeF) ? count($productTypeF) : -1;
						if ($fixCountProduct > 0 && in_array($productFixID, $displayedData['block' . $i]['productIDs'], false)) {
							$displayedData['block' . $i]['lenght'] += 1;
						} else {
							$i++;
							$displayedData['block' . $i] = [];
							$displayedData['block' . $i]['lenght'] = 1;
//							$displayedData['block' . $i]['productIDs'] = $productTypeF;
							$displayedData['block' . $i]['productIDs'] = $productFixIDs;
							$displayedData['block' . $i]['nbrProducts'] = $fixCountProduct;
							$displayedData['block' . $i]['type'] = 'F';
							$displayedData['block' . $i]['type_name'] = __d('CompareAstelBe', 'fix');
							$displayedData['block' . $i]['product'] = $product;
						}
						
						$productTvIDs = Hash::combine($product, 'product_T.{n}.product_id', 'product_T.{n}.product_id');
//						$productTypeTv = $play['play_description']['tv'];
						$productTvID = getFirst($productTvIDs);
						$tvCountProduct = !empty($productTypeTv) ? count($productTypeTv) : -1;
						if ($tvCountProduct > 0 && in_array($productTvID, $displayedData['block' . $i]['productIDs'], false)) {
							$displayedData['block' . $i]['lenght'] += 1;
						} else {
							$i++;
							$displayedData['block' . $i] = [];
							$displayedData['block' . $i]['lenght'] = 1;
//							$displayedData['block' . $i]['productIDs'] = $productTypeTv;
							$displayedData['block' . $i]['productIDs'] = $productTvIDs;
							$displayedData['block' . $i]['nbrProducts'] = $tvCountProduct;
							$displayedData['block' . $i]['type'] = 'T';
							$displayedData['block' . $i]['type_name'] = __d('CompareAstelBe', 'television');
							$displayedData['block' . $i]['product'] = $product;
						}
						
						
						// Initialize the last brand name variable
						$lastBrandName = '';
						
						$x = 1;
						while ($x <= count($displayedData)) {
							$y = $x + 1;
							while ($y <= count($displayedData)) {
								if ($displayedData['block' . $x]['nbrProducts'] != -1) {
									$displayedData['block' . $x]['lenght'] = $displayedData['block' . $x]['lenght'] + $displayedData['block' . $y]['lenght'];
									
									// Retrieve the cashback total
									$cashbackTotal = $product['total_pricings']['total_cashback'];
									
									// Display the cashback total
									echo "<h2 class='float-right py-1 px-3' style='color:#fff; background-color: #f23078;'>€ " . $cashbackTotal . " cashback</h2><br>";
									
									// Check if the brand name is different from the last one displayed
									if ($lastBrandName !== $play['brand_name']) {
										echo "<div class='col-md-9'>";
										echo "<div class='row h-100'>";
										
										// Display the brand name
										echo "<h2 class='mt-4'>{$play['brand_name']}</h2>";
										
										echo "</div>";
										echo "</div>";
										
										// Update the last brand name variable
										$lastBrandName = $play['brand_name'];
									}
									
									unset($displayedData['block' . $y]);
								}
								$y++;
							}
							$x++;
						}
						
//						$thisCombinationType = Set::extract('combinations_type', $product);
//						debug($thisCombinationType);
						
						
						?>
<!--					<div class='col-md-9'>-->
<!--						<div class="row h-100">-->
<!--							--><?php
//									echo "<h2 class='mt-4'>{$play['brand_name']}</h2>";
//
//
////							echo '<pre>';
////							print_r($displayedData);
////							echo '</pre>';
////							echo "<h2 class='mt-4'>{$play['brand_name']}</h2>";
//
//							?>
<!--						</div>-->
<!--					</div>-->
					
<?php
						
//						$includedMinutes = $play['play_description']['play']['included_minutes_calls'];
//						$includedDataVolume = $play['play_description']['play']['included_data_volume'];
//						$includedDataVolumeFormatted = round($includedDataVolume / 1000, 1);
//						$cashbackTotal = $product['total_pricings']['total_cashback'];
						//debug($cashbackTotal);
//						echo "<h2 class='float-right py-1 px-3' style='color:#fff; background-color: #f23078;'>€ " . $cashbackTotal . " cashback</h2><br>";
						
//						if ($includedDataVolumeFormatted !== 0 && $includedDataVolumeFormatted !== null && $includedDataVolumeFormatted !== '') {
//
//							// Add cashback amount to the total
////							$cashbackTotal += $play['commission']['cashback_amount'];
////							$cashbackTotal += $play['total_pricings']['total_cashback'];
//
//							// Output the merged cashback
////							echo "<h2>Merged Cashback: € {$cashbackTotal}</h2>";
//
//							// If I have more than one cashbacks , I need to merge them , so I output just one
////							echo "<h2 class='float-right py-1 px-3' style='color:#fff; background-color: #f23078;'>€ " . $cashbackTotal . " cashback</h2><br>";
//
//							echo "<div class='border border-info p-3'>";
//
//							// Output only if it's the first occurrence of the brand
//							if ($mobileBrandName !== $play['brand_name']) {
//								// if included data equals 0 , not show the brand name
//								if ($includedDataVolumeFormatted !== 0) {
//									echo "<h2 class='mt-4'>{$play['brand_name']}</h2>";
//									$mobileBrandName = $play['brand_name'];
//								}
//							}
//
//							echo "<div>GSM: " . __d('CoreAstelBe', '%s GB', [$includedDataVolumeFormatted]) . "</div>";
//
//							echo "<div>" . __d('CompareAstelBe', 'Included Minutes: %s', $play['play_description']['play']['included_minutes_calls']) . "</div>";
//							echo "<div>" . __d('CompareAstelBe', 'Included SMS: %s', $play['play_description']['play']['included_sms']) . "</div>";
//							echo "</div>";
//						}
					}
					?>
					
<!--					<div class="d-flex justify-content-center mt-2">-->
<!--						<h1>+</h1>-->
<!--					</div>-->
					
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
					
<!--					<div class="d-flex justify-content-center mt-2">-->
<!--						<h1>=</h1>-->
<!--					</div>-->
					
<!--					<div>TOTAL: --><?//= $product['total_pricings']['total_price'] ?><!-- pendant XX mois</div>-->
<!--					<div>puis --><?//= $product['total_pricings']['total_price_without_discount'] ?><!-- /mois TVAC</div>-->
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>







