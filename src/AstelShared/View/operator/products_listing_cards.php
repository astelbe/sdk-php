<?php

use CakeUtility\Hash;
//debug($params);
//debug($params['products']);

?>
<div class="row">
<?php
//debug($params);
foreach ($params['products'] as $key => $product) {
	
	$brandName = $product['brand_name'];
	$shortName = $product['short_name'];
	$totalCashback = $product['total_cashback'];
	
	$totalPrice = $product['total_price_without_discount'];
	$discountedPrice = $product['total_price'];
	$totalPriceDuration = $product['total_price_discount_duration'];
	
	$orderUrl = $product['order_url'];
	$quality_score = $product['quality_score'];
	$full_activation_price = $product['full_activation_pack_price'];
	//debug($full_activation_price);
	
	$full_setup_price = $product['full_total_setup_price'];
//	debug($full_setup_price);
	$reduced_setup_price = $product['reduced_total_setup_price'];
//	debug($reduced_setup_price);
	$total_savings = $product['products_total_savings'];
	
	$toggle = $product['toggle'];
	
	// mobile
	$mobile = $product['play_description']['mobile'];
	$mobileData = $mobile['included_data_volume'];
	$mobileMinuteCalls = $mobile['included_minutes_calls'];
	$mobileSms = $mobile['included_sms'];
	$mobilePriceDescription = $mobile['price_description'];
	
	// internet
	$internet = $product['play_description']['internet'];
	$internetSpeed = $internet['bandwidth_download'];
	$internetVolume = $internet['bandwidth_volume'];
	$internetDescription = $internet['price_description'];
	
	// tv
	$tv = $product['play_description']['tv'];
	$tvChannels = $tv['number_tv_channel'];
	$tvDescription = $tv['price_description'];
	
	// tv decoder
	$tvDecoderApplication = $tv['size_number_tv_max'];

	// fix
	$fix = $product['play_description']['fix'];
	$fixMinutesCalls = $fix['included_minutes_calls'];
	$fixDescription = $fix['price_description'];
	
	
	
	?>
	<div class="col-md-3 mb-5">
		<div class="px-3 py-4 shadow h-100 rounded-lg">
<!--		<div class="d-flex flex-column justify-content-between shadow rounded-lg">-->
			<div class="py-1 px-2 position-relative rounded-sm" style="color:#fff; background-color: #f23078; left:100px; width: 130px;"><?= $product['total_cashback']?></div>
<!--			<div>--><?//= $toggle ?><!--</div>-->
			<h2>
				<?= $product['brand_name'];?>
			</h2>
			<p>
				<?= $product['short_name'];?>
			</p>
			
			<p>gsm:</p>
			<div class="d-inline-flex p-2">
				<?= $product['mobile']['included_data_volume']; ?>
				<?= $product['mobile']['included_sms'] ?>
				<?= $product['mobile']['included_minutes_calls'] ?>
			</div>
			<div class="col-lg-12">
<!--				<span class="sub-details-infos toggle-details toggle-details-'--><?//=$key?><!--'">-->
					<?= $product['mobile']['price_description'] ?>
<!--				</span>-->
			</div>
			
			
	<?php
	
//	echo '<div class="my-5">' . $toggle . '</div>';
	
	if (isset($mobileMinuteCalls) || isset($mobileSms)) {
		?>
			<div class="col-lg-12">
				<h4>GSM:</h4>
					<div class="d-inline-flex p-2">
					<?= $mobileData ?>
					<?= $mobileMinuteCalls ?>
					<?= $mobileSms ?>
					</div>
					<div class="col-lg-12">
						<?= $mobilePriceDescription ?>
					</div>
				</div>
		<?php
	}
	
	if (isset($internetSpeed) || isset($internetVolume)) {
		?>
			<div class="col-lg-12 mt-4">
				<h4>Internet:</h4>
					<div class="d-inline-flex p-2">
					<?= $internetSpeed ?>
					<?= $internetVolume ?>
					</div>
					<div class="col-lg-12">
						<?= $internetDescription ?>
					</div>
				</div>
		<?php
	}
	
	if (isset($tvChannels)) {
		?>
			<div class="col-lg-12 mt-4">
				<h4>TV:</h4>
					<div class="d-inline-flex flex-column-reverse p-2">
					<?= $tvDecoderApplication ?>
					<?= $tvChannels ?>
					</div>
					<div class="col-lg-12">
						<?= $tvDescription ?>
					</div>
			</div>
		<?php
	}
	
	// fix
	if (!empty($fixMinutesCalls)) {
		?>
		<div class="col-lg-12 mt-4">
			<h4>Fix:</h4>
				<div class="d-inline-flex p-2">
				<?= $fixMinutesCalls ?>
				</div>
				<div class="col-lg-12">
					<?= $fixDescription ?>
				</div>
			</div>
		<?php
	}
	
	echo '<p class="result-setup-price mt-5">';

	echo $quality_score;
	echo '<h5>TOTAL : </h5>';
	echo $discountedPrice;
	echo $totalPriceDuration;
	echo '<span>, puis ' . $totalPrice . '</span>';
	echo '<div><span>Activation et installation : </span>';
	echo $full_setup_price;
	echo $reduced_setup_price;
	echo '<div>' . $total_savings . '</div>';
	echo '</div>';
	echo '</p>';
	echo  $orderUrl;

	?>
		</div>
	</div>
<?php
}
?>
</div>

