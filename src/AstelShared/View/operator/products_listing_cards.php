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
	<?php
	$lastBrandName = null;
	foreach ($params['products'] as $product) : ?>
	<div class="col-md-12">
<!--		<div class="col-md-3"> 4cols-->
		<!--		2cols-->
<!--		<div class="col-md-6">-->
		<!--	3cols-->
<!--		<div class="col-md-4">-->
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
						
						$brandName = $play['brand_name'];
//						//debug($brandName);
//
//						// Skip if the brand name is -1 or empty
//						if ($brandName === -1 || empty($brandName)) {
//							continue;
//						}
						
						// Count the number of products for each brand
//						$brandCounts[$brandName] = isset($brandCounts[$brandName]) ? $brandCounts[$brandName] + 1 : 1;
//						debug($brandCounts);
						
						$nbrProducts = $product['total_pricings']['number_products'];
						$nbrProviders = $product['total_pricings']['number_providers'];
						
						$displayedData['product'] = $product;
						$displayedData = [];
						$i = 1;
						
						//$productMobileIDs = $play['id'];
						//debug($product);
						//$productMobileIDs = Hash::combine($product, 'product_M.{n}.product_id', 'product_M.{n}.product_id');
						$displayedData['block' . $i] = [];
						$displayedData['block' . $i]['lenght'] = 1;
						$displayedData['block' . $i]['products']['id'][] = $play['id'];
						$displayedData['block' . $i]['nbrProducts'] = !empty($play['play_description']['play']) ? count($play['play_description']['play']) : -1;
						$displayedData['block' . $i]['type'] = 'M';
						$displayedData['block' . $i]['type_name'] = __d('CompareAstelBe', 'mobile');
						
//						$productInternetIDs = Hash::combine($product, 'products.products.id', 'products.products.id');
//						$productInternetID = getFirst($productInternetIDs);
						$internetCountProduct = !empty($play['play_description']['internet']) ? count($play['play_description']['internet']) : -1;
						if ($internetCountProduct > 0 && in_array($productInternetID, $displayedData['block' . $i]['productIDs'], false)) {
							$displayedData['block' . $i]['lenght'] += 1;
						} else {
							$i++;
							$displayedData['block' . $i] = [];
							$displayedData['block' . $i]['lenght'] = 1;
							$displayedData['block' . $i]['products']['id'][] = 	$play['id'];
							$displayedData['block' . $i]['nbrProducts'] = $internetCountProduct;
							$displayedData['block' . $i]['type'] = 'I';
							$displayedData['block' . $i]['type_name'] = __d('CompareAstelBe', 'internet');
						}
						
						$productFixIDs = Hash::combine($product, 'product_F.{n}.product_id', 'product_F.{n}.product_id');
						$productFixID = getFirst($productFixIDs);
						$fixCountProduct = !empty($productTypeF) ? count($productTypeF) : -1;
						if ($fixCountProduct > 0 && in_array($productFixID, $displayedData['block' . $i]['productIDs'], false)) {
							$displayedData['block' . $i]['lenght'] += 1;
						} else {
							$i++;
							$displayedData['block' . $i] = [];
							$displayedData['block' . $i]['lenght'] = 1;
							$displayedData['block' . $i]['products']['id'][] = $play['id'];
							$displayedData['block' . $i]['nbrProducts'] = $fixCountProduct;
							$displayedData['block' . $i]['type'] = 'F';
							$displayedData['block' . $i]['type_name'] = __d('CompareAstelBe', 'fix');
							$displayedData['block' . $i]['product'] = $product;
						}
						
						$productTvIDs = Hash::combine($product, 'product_T.{n}.product_id', 'product_T.{n}.product_id');
						$productTvID = getFirst($productTvIDs);
						$tvCountProduct = !empty($productTypeTv) ? count($productTypeTv) : -1;
						if ($tvCountProduct > 0 && in_array($productTvID, $displayedData['block' . $i]['productIDs'], false)) {
							$displayedData['block' . $i]['lenght'] += 1;
						} else {
							$i++;
							$displayedData['block' . $i] = [];
							$displayedData['block' . $i]['lenght'] = 1;
							$displayedData['block' . $i]['products']['id'][]= $play['id'];
							$displayedData['block' . $i]['nbrProducts'] = $tvCountProduct;
							$displayedData['block' . $i]['type'] = 'T';
							$displayedData['block' . $i]['type_name'] = __d('CompareAstelBe', 'television');
							$displayedData['block' . $i]['product'] = $product;
						}
						
						// If there are two blocks for the same product, merge them
						$x = 1;
						while ($x <= count($displayedData)) {
							$y = $x + 1;
							while ($y <= count($displayedData)) {
								if ($displayedData['block' . $x]['nbrProducts'] != -1) {
									$displayedData['block' . $x]['lenght'] = $displayedData['block' . $x]['lenght'] + $displayedData['block' . $y]['lenght'];
									
									
									// Retrieve the cashback total
									$cashbackTotal = $product['total_pricings']['total_cashback'];
									
									// Display the cashback total
									echo"<div class='d-flex'>";
										echo "<h2 class='py-1 px-3 ml-auto' style='color:#fff; background-color: #f23078;'>€ " . $cashbackTotal . " cashback</h2><br>";
									echo "</div>";



									unset($displayedData['block' . $y]);
								}
								$y++;
							}
							$x++;
						}
						
						
//						<!-- block div wrapper that contain the combinations -->
						echo "<h2 class='mt-4'>{$brandName}</h2>";
						echo $displayedData['block' . $x]['type_name'];
						echo '<pre>';
						print_r($displayedData);
						echo '</pre>';
//						<!-- end row result -->

						$index++;
					}
					
					//debug($displayedData);
					
					// Loop through the brand counts to display the brand name if the conditions are met
//					debug($displayedData);
//					$displayedData = Hash::extract($displayedData, '{n}[nbrProducts > 0]');
//					foreach ($displayedData as $blockName => $block) {
//						if ($block['nbrProducts'] !== -1) {
//
//							debug($displayedData);
//
////							$typeName = Hash::extract($displayedData, '{n}.type_name');
////							debug($typeName);
////							debug($block);
//
//							$countPThisBlock = count($play['id']);
////							debug($countPThisBlock);
//
//							foreach ($block['products']['id'] as $productID) {
//
//
////								$p = Hash::get($block, $productID);
////								debug((int)$block['type_name']['Internet']);
//
////								debug($productID);
//								//debug($displayedData);
//							}
//
//							echo "<h2 class='mt-4'>{$play['brand_name']}</h2>";
////							echo '<pre>';
////							print_r($displayedData);
////							echo '</pre>';
//						}
//					}
					?>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>







