<?php

use CakeUtility\Hash;
// debug($params);
/**
 * Common V1/V2 template used to display
 * - packs
 * - mobile
 * - internet
 * Specific displays are available for packs, as the columns are not the same kind
 * Mobile and Internet custom_columns are separated by each play characteristic
 * Packs columns are separate by play types
 * This template contains desktop and mobile version. Some labels are only displayed on mobile version
 * IE of required data :
 * ['play_type' => [
 *    'play_type' => 'play_name',
 *    'title' => 'tab title',
 *    'custom-col' => [[
 *        'name' => 'column title',
 *        'key_of_value' => 'product data key to display (custom)',
 *        'responsive_label' => 'label for data - only for internet and mobile tab',
 *    ],]]
 * Params options :
 * - display_col_logo (bool) default false : add a col with logo for multi-brand listing
 * - disabled_product_link (bool)  default false : product are not clickable, for INTBRU who hasn't single product page
 * - display_operator_in_product_name (bool) default false : prefixe product name with operator, for multi-brand listing
 */

$is_pack = $params['tab_type'] === 'packs';
$params['bonus_header'] = [
	// 'packs_column_internet' => 'Bonus ' . self::getTranslation('general', 'internet', $params['version']),
	// 'packs_column_mobile' => 'Bonus ' . self::getTranslation('general', 'mobile', $params['version']),
	// 'packs_column_tv' => 'Bonus ' . self::getTranslation('general', 'tv', $params['version']),

	'packs_column_internet' => __d('product', 'details', self::getTranslation('general', 'internet', $params['version'])),
	'packs_column_mobile' => __d('product', 'details', self::getTranslation('general', 'mobile', $params['version'])),
	'packs_column_tv' => __d('product', 'details', self::getTranslation('general', 'tv', $params['version'])),
];
?>

<section class="operator-products-table mb-2">
	<?php if (Hash::get($params, 'title', false)) { ?>
		<h2 class="mt-2 mb-4 mb-lg-3 text-center text-lg-left">
			<?= $params['title']; ?>
		</h2>
	<?php } ?>

	<header class="row d-none my-2 no-gutters p-2 d-lg-flex align-items-end text-center border-bottom border-darkblue text-darkblue font-weight-bold bg-lightblue" style="font-size:0.7rem; border-radius: 0.5rem;">
		<?php if (Hash::get($params, 'options.display_col_logo', false) === true) { ?>
			<div class="col-1">
			</div>
		<?php } ?>
		<?php
		// Generate the specifics data columns
		foreach ($params['custom-col'] as $col) { ?>
			<div class="col text-left<?= ($is_pack ? ' pl-1' : '') ?> fs094">
				<?php if ($col['name'] == 'download_speed' && $params['language'] == 'NL') { ?>
					<div style="margin-top:-31px;">
						<?= $col['name'] ?>
					</div>
				<?php } else { ?>
					<?= $col['name'] ?>
				<?php } ?>
			</div>
			<?php
			// add internet and gsm or fix bonus column
			if (in_array($params['code'], ['IT', 'MI'])) {
				echo '<div class="col text-left fs094">' . $params['bonus_header'][$col['key_of_value']] . '</div>';
			} ?>
		<?php } ?>
		<?php if (!in_array($params['code'], ['MIT', 'MFIT', 'IT', 'MI', 'FI', 'FT', 'FIT'])) { ?>
			<div class="col-lg-2 fs094">
				<?= $params['col-headers']['bonus'] ?>
			</div>
		<?php } ?>
		<div class="col-lg-2 fs094">
			<?= $params['col-headers']['pack_price'] ?>
		</div>
		<div class="col-lg-2 fs094">
			<?= $params['col-headers']['cashback_and_order'] ?>
		</div>
	</header>

	<?php foreach ($params['products'] as $k => $product) {
		if (isset($params['max_length']) && $k >= $params['max_length']) {
			break;
		}
	?>
		<!-- DESKTOP -->
		<div id="product<?= Hash::get($product, 'id') ?>" class="pt-1">
			<article class="d-none d-lg-block my-2 no-gutters align-items-start text-<?= Hash::get($product, 'brand_slug') ?>-wrapper">
				<h3 class="mb-1 p-1 font-weight-bold font-s-1 color-operator text-<?= Hash::get($product, 'brand_slug') ?>">
					<?php if (Hash::get($params, 'show_index', false) === true) { ?>
						<span class="pr-3">N°<?= $k + 1 ?></span>
					<?php } ?>
					<?php if ($params['version'] != 'cake' && Hash::Get($params, 'options.disabled_product_link', false) != true) { ?>
						<a class="color-operator text-<?= Hash::get($product, 'brand_slug') ?> gtm-product-click" href="<?= Hash::get($product, 'web.product_sheet_url.' . $params['language']) ?>" data-product-details='<?= json_encode($product['gtm_product_click_details']) ?>'>
						<?php } ?>
						<?php if (Hash::Get($params, 'options.display_operator_in_product_name', false)) {
							echo Hash::get($product, 'brand_name') . ' ';
						} ?>
						<?= Hash::get($product, 'short_name.' . $params['language']); ?>
						<?php if ($params['version'] != 'cake' && Hash::Get($params, 'options.disabled_product_link', false) != 1) { ?>
						</a>
					<?php } ?>
					<?php if (Hash::get($params, 'options.display_quality_stars', false) === true) { ?>
						<div class="position-relative d-inline-block cursor-pointer modalClick" data-toggle="modal" data-target="#modalQualityStars" style="display:inline-block">
							<div class="ml-3"><?= self::renderStar($product['quality_score']) ?>
								<i class="fa fa-info pl-2"></i>
							</div>
						</div>
					<?php } ?>
				</h3>
				<div class="row no-gutters p-2">
					<?php if (Hash::get($params, 'options.display_col_logo', false) === true) { ?>

						<div class="col-1">
							<img width="80" src="<?= Hash::get($product, 'brand.fact_sheet.logo.small') ?>" alt="<?= Hash::get($product, 'brand.name') ?>" />
						</div>
					<?php } ?>

					<?php
					// Generate specifics data columns
					/**
					 * If packs, $col['key_of_value'] contains the data already processed for each plays
					 * If solo, $col['key_of_value'] is the $product array path to the data
					 */
					foreach ($params['custom-col'] as $col) {
					?>
						<div class="col<?= ($is_pack ? ' pl-1 text-left' : '') ?>">
							<?php if ($is_pack) { ?>
								<b><?= $col['responsive_label'] ?></b>
								<?= $product[$col['key_of_value']] ?>
							<?php } else { ?>
								<?= self::getTranslatedPlayDescription($col['key_of_value'], $product, $params['version']); ?>
							<?php } ?>
						</div>
						<?php
						// add internet and gsm or fix bonus column
						if (in_array($params['code'], ['IT', 'MI'])) {
							switch ($col['key_of_value']) {
								case 'packs_column_internet':
									echo '<div class="col pl-1">' . Hash::get($product, 'play_description.internet.price_description.' . $params['language'], '-') . '</div>';
									break;
								case 'packs_column_mobile':
									echo '<div class="col pl-1">' . Hash::get($product, 'play_description.mobile.price_description.' . $params['language'], '-') . '</div>';
									break;
								case 'packs_column_tv':
									echo '<div class="col pl-1">' . Hash::get($product, 'play_description.tv.price_description.' . $params['language'], '-') . '</div>';
									break;
							}
						} ?>

					<?php } ?>
					<?php if (!$is_pack) { ?>
						<div class="col-lg-2">
							<?php
							$price_description = Hash::get($product, 'play_description.' . $params['play_type'] . '.price_description.' . $params['language'], '-');
							if (!empty($price_description)) {
								echo $price_description;
							} else {
								echo '<div class="text-center">-</div>';
							}
							?>
						</div>
					<?php } ?>
					<div class="col-lg-2 text-center">
						<?= $product['displayed_price_desktop'] ?>
					</div>
					<div class="col-lg-2 text-center">

						<?php if (Hash::get($product, 'displayed_cashback', false)) { ?>
							<div class="mb-2 cursor-pointer">
								<?= Hash::get($product, 'displayed_cashback'); ?>
							</div>
						<?php } ?>

						<?php if (Hash::Get($params, 'options.display_activation_time', false)) { ?>
							<div class="mb-2">
								<?= __d('product', 'Max activation time', ['%operator' => $product['brand_name'], '%activation_time' => $product['max_activation_time']]) ?>
							</div>
						<?php } ?>

						<button class="blueBtn darkBlueBtn blueBtn_s_fullRounded mx-auto fw700">Commander</button>

						<!-- <?= $product['order_button'] ?> -->
						<div class="font-s-08 mt-1">
							<?= $product['activation_price'] ?>
						</div>

					</div>
				</div>
			</article>
			<hr>

			<!-- MOBILE -->
			<article class="d-lg-none my-3 border text-<?= Hash::get($product, 'brand_slug') ?>-wrapper">
				<h3 class="font-weight-bold color-operator bg-lighter p-2 mb-2">
					<?php if ($params['version'] != 'cake' && Hash::Get($params, 'options.disabled_product_link', false) != 1) { ?>
						<a class="color-operator gtm-product-click" href="<?= Hash::get($product, 'web.product_sheet_url.' . $params['language']) ?>" data-product-details='<?= json_encode($product['gtm_product_click_details']) ?>'>
						<?php } ?>
						<?php if (Hash::get($params, 'show_index', false) === true) { ?>
							<span class="pr-3">N°<?= $k + 1 ?></span>
						<?php } ?>
						<?php if (Hash::Get($params, 'options.display_operator_in_product_name', false)) {
							echo Hash::get($product, 'brand_name') . ' ';
						} ?>
						<?= Hash::get($product, 'short_name.' . $params['language']); ?>
						<?php if ($params['version'] != 'cake' && Hash::Get($params, 'options.disabled_product_link', false) != 1) { ?>
						</a>
					<?php } ?>
					<?php if (Hash::get($params, 'options.display_quality_stars', false) === true) { ?>
						<div class="position-relative d-inline-block cursor-pointer modalClick" data-toggle="modal" data-target="#modalQualityStars" style="display:inline-block">
							<div class="my-2"><?= self::renderStar($product['quality_score']) ?>
								<i class="fa fa-info pl-2"></i>
							</div>
						</div>
					<?php } ?>
				</h3>
				<section class="row no-gutters px-2" class="text-left">
					<?php
					$n_col = ($is_pack !== false && count($col) > 1 ? '6' : '12');
					// Generate the specifics data columns
					foreach ($params['custom-col'] as $k => $col) { ?>
						<div class="col-<?= $n_col ?> <?= ($is_pack ? 'mb-2' : '') ?> mb-2">
							<?php if ($is_pack) { ?>
								<div class="mb-1 font-weight-bold"><?= $col['name'] ?></div>
							<?php } ?>
							<?= $col['responsive_label'] ?>
							<?php if ($is_pack) { ?>
								<?= $product[$col['key_of_value']] ?>
							<?php } else { ?>
								<?= self::getTranslatedPlayDescription($col['key_of_value'], $product, $params['version']); ?>
							<?php } ?>
						</div>
					<?php } ?>
					<?php
					$price_description = Hash::get($product, 'play_description.' . $params['play_type'] . '.price_description.' . $params['language']);
					if ($price_description) {
						echo '<p class="mt-2" style="font-size:12px;">' . $price_description . '</p>';
					}
					?>
				</section>
				<section class="text-center pt-3 bg-lighter">
					<div class="font-s-11 mb-3">
						<?= $product['displayed_price_responsive'] ?>
					</div>
					<div class="text-center">
						<div class="pb-3">
							<?= $product['activation_price'] ?>
						</div>
						<?php if (Hash::get($product, 'displayed_cashback', false)) { ?>
							<div class="mb-3">
								<?php echo Hash::get($product, 'displayed_cashback'); ?>
							</div>
						<?php } ?>
						<?php if (Hash::Get($params, 'options.display_activation_time', false)) { ?>
							<div class="mb-2">
								<?= __d('product', 'Max activation time', ['%operator' => $product['brand_name'], '%activation_time' => $product['max_activation_time']]) ?>
							</div>
						<?php } ?>
						<div class="pb-3">
							<?= $product['order_button'] ?>
						</div>
					</div>
				</section>
			</article>
		</div>
	<?php } ?>
</section>
<?php if (Hash::get($params, 'options.display_quality_stars', false) === true) { ?>
	<div class="modal fade" id="modalQualityStars" tabindex="-1" role="dialog" aria-labelledby="modalQualityStars" style="display: none;" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">
						<?php echo __d('general', 'quality_modal_title'); ?>
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">
					<?php echo __d('general', 'quality_info'); ?>
				</div>
			</div>
		</div>
	<?php } ?>