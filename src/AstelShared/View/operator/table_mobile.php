<?php
use CakeUtility\Hash;
// TODO move helpers in sharedView
?>

<section class="operator-products-table">
	<h2 class="my-3">
		<span class="d-inline-block pr-2">
			<?= $params['title']; ?>
		</span>
	</h2>

	<header class="row d-none my-1 no-gutters d-lg-flex align-items-end text-center border-bottom border-blue text-blue font-weight-bold"
			style="font-size:0.7rem;">
		<div class="col-lg-2 text-left">
			<?= $params['col-headers']['product'] ?>
		</div>
		<?php
		// Generate the specifics data columns
		foreach ($params['custom-col'] as $col) { ?>
			<div class="col-lg-1">
				<?php if ($col['name'] == 'download_speed' && $params['language'] == 'NL') { ?>
					<div style="margin-top:-31px;">
						<?= $col['name'] ?>
					</div>
				<?php } else { ?>
					<?= $col['name'] ?>
				<?php } ?>
			</div>
		<?php } ?>
		<div class="col-lg-2">
			<?= $params['col-headers']['bonus'] ?>
		</div>
		<div class="col-lg-2">
			<?= $params['col-headers']['pack_price'] ?>
		</div>
		<div class="col-lg-3">
			<?= $params['col-headers']['cashback_and_order'] ?>
		</div>
	</header>
	
	<?php foreach ($params['products'] as $k => $product) {
		//debug($product);
		?>
		<!-- DESKTOP -->
		<article class="row d-none d-lg-flex my-2 no-gutters align-items-start text-center border-bottom ">
			<div class="col-lg-2 text-left">
				<h3 class="mb-0 font-weight-bold">
					<a class="color-operator" href="<?= Hash::get($product, 'web.product_sheet_url.' . $params['language']) ?>">
						<?php
						echo Hash::get($product, 'short_name.' . $params['language']);
						?>
					</a>
				</h3>
			</div>
			<?php
			// Generate the 3 specifics data columns
			foreach ($params['custom-col'] as $col) { ?>
				<div class="col-lg-1">
					<?= GeneralHelper::getProductInfo($col['key_of_value'], $product); ?>
				</div>
			<?php } ?>

			<div class="col-lg-2">
				<?php
				//debug($play_type);
				$price_description = Hash::get($product, 'play_description.' . $params['play_type'] . '.price_description.' . $params['language']);
				if ($price_description) {
					echo $price_description;
				}
				?>
			</div>
			<div class="col-lg-2">
				<?= GeneralHelper::getDisplayedPrice($product, ['color-css-class' => 'color-operator', 'br-before-during-month' => true]) ?>
			</div>
			<div class="col-lg-3 mt-2 text-center">

				<div class="mb-2 cursor-pointer" data-toggle="modal" data-target="#modalExplainCashback">
					<?php
					$cashbackAmount = Hash::get($product, 'commission.cashback_amount', 0);
					if ($cashbackAmount != 0) {
						echo ucfirst(strtolower(__d('general', 'header_cashback')));
						echo GeneralHelper::cashbackBubble($cashbackAmount, 'sm'); ?>
						<i class="fa fa-info pl-2"></i>
					<?php } ?>
				</div>

				<?= GeneralHelper::orderButton($product, ['class_btn_a' => 'mb-1', 'class_btn_span' => 'font-s-09']) ?>

				<?= GeneralHelper::getProductActivationPrice($product); ?>
			</div>
		</article>

		<!-- MOBILE -->
		<article class="row d-flex d-lg-none my-2 border-bottom pb-3">
			<section class="col-6" class="text-left">
				<h3 class="font-weight-bold">
					<a class="color-operator"
							href="<?= Hash::get($product, 'web.product_sheet_url.' . $params['language']) ?>">
						<?php
						echo Hash::get($product, 'short_name.' . $params['language']);
						?>                    </a>
				</h3>
				<?php
				// Generate the 3 specifics data columns
				foreach ($params['custom-col'] as $col) {
					?>
					<div>
						<b><?= __d('product', 'tab_internet_label_'. $k) . '</b> ' .
						GeneralHelper::getProductInfo($col['key_of_value'], $product, '_responsive'); ?>
					</div>
				<?php } ?>
				
				<?php
				$price_description = Hash::get($product, 'play_description.' . $play_type . '.price_description.' . Config::read('App.language'));
				if ($price_description) {
					//						echo '<p><b>' . __d('product', 'Bonus') . ':</b><br>' . $price_description . '</p>';
					echo '<p class="mt-2">' . $price_description . '</p>';
				}
				?>
			</section>
			<section class="col-6 text-center">
				<div class="font-s-11">
					<?= GeneralHelper::getDisplayedPrice($product, ['color-css-class' => 'color-operator']) ?>
				</div>
				<div class="text-center">
					<div class="mb-2" data-toggle="modal" data-target="#modalExplainCashback">
						<?php
						$cashbackAmount = Hash::get($product, 'commission.cashback_amount', 0);
						if ($cashbackAmount != 0) {
							echo ucfirst(strtolower(__d('general', 'header_cashback')));
							echo GeneralHelper::cashbackBubble($cashbackAmount, 'sm'); ?>
							<i class="fa fa-info pl-2"></i>
						<?php } ?>
					</div>

					<?= GeneralHelper::orderButton($product, ['class_btn_a' => 'mb-1', 'class_btn_span' => 'font-s-09']) ?>

					<?= GeneralHelper::getProductActivationPrice($product, $VATD); ?>
				</div>
			</section>
		</article>
	<?php } ?>
</section>