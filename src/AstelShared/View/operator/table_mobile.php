<?php
use CakeUtility\Hash;

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
 *	'play_type' => 'play_name',
 *	'title' => 'tab title',
 *	'custom-col' => [[
 *		'name' => 'column title',
 *		'key_of_value' => 'product data key to display (custom)',
 *		'responsive_label' => 'label for data - only for internet and mobile tab',
 *	],]]
 */

$is_pack = $params['play_type'] === 'packs';
?>

<section class="operator-products-table mb-5">
	<?php if(Hash::get($params, 'title', false)) { ?>
		<h2 class="mt-2 mb-4 mb-lg-3 text-center text-lg-left">
			<span class="d-inline-block pr-2">
				<?= $params['title']; ?>
			</span>
            <span class="d-inline-block pl-3 mt-2"><?= $params['fa-icon'] ?></span>
		</h2>
	<?php } ?>

	<header class="row d-none my-2 no-gutters p-2 d-lg-flex align-items-end text-center border-bottom border-blue text-blue font-weight-bold"
			style="font-size:0.7rem;">
		<?php if (Hash::get($params, 'display_col_logo', false) === true) { ?>
			<div class="col-1">
			</div>
		<?php } ?>
		<?php
		// Generate the specifics data columns
		foreach ($params['custom-col'] as $col) { ?>
			<div class="col text-left<?= ($is_pack ? ' pl-1' : '') ?>">
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
                echo '<div class="col text-left">' . $params['bonus_header'][$col['key_of_value']] . '</div>';
            } ?>
		<?php } ?>
        <?php  if (!in_array($params['code'], ['IT', 'MI'])) { ?>
            <div class="col-lg-2">
                <?= $params['col-headers']['bonus'] ?>
            </div>
        <?php } ?>
		<div class="col-lg-2">
			<?= $params['col-headers']['pack_price'] ?>
		</div>
		<div class="col-lg-2">
			<?= $params['col-headers']['cashback_and_order'] ?>
		</div>
	</header>
	
	<?php foreach ($params['products'] as $k => $product) { ?>
		<!-- DESKTOP -->
		<article class="d-none d-lg-block my-2 no-gutters align-items-start text-<?= Hash::get($product, 'brand_slug') ?>-wrapper">
			<h3 class="mb-1 bg-lighter p-1 font-weight-bold font-s-1 color-operator text-<?= Hash::get($product, 'brand_slug') ?>">
				<?php if ($params['version'] != 'cake') { ?>
				<a class="color-operator text-<?= Hash::get($product, 'brand_slug') ?>" href="<?= Hash::get($product, 'web.product_sheet_url.' . $params['language']) ?>">
					<?php } ?>
					<?php if(Hash::Get($params, 'options.display_operator_in_product_name', false)) {
						echo Hash::get($product, 'brand_name') . ' ';
					} ?>
					<?php
					echo Hash::get($product, 'short_name.' . $params['language']);
					?>
					<?php if ($params['version'] != 'cake') { ?>
				</a>
			<?php } ?>
            </h3>
			<div class="row no-gutters p-2">
				<?php if (Hash::get($params, 'display_col_logo', false) === true) { ?>

					<div class="col-1">
						<img width="80" src="<?= Hash::get($product, 'brand.fact_sheet.logo.small') ?>" alt="<?= Hash::get($product, 'brand.name') ?>" />
					</div>
				<?php } ?>

				<?php
				// Generate specifics data columns
				foreach ($params['custom-col'] as $col) {
					?>
					<div class="col<?= ($is_pack ? ' pl-1 text-left' : '') ?>">
						<?php if($is_pack) { ?>
							<b><?= $col['responsive_label'] ?></b>
						<?php } ?>
						<?= self::getProductInfo($col['key_of_value'], $product, $params['version']); ?>
					</div>
                    <?php
                   // add internet and gsm or fix bonus column
                        if (in_array($params['code'], ['IT', 'MI'])) {
                        switch ($col['key_of_value']) {
                            case 'packs_column_internet' :
                                echo '<div class="col pl-1">' . Hash::get($product, 'play_description.internet.price_description.' . $params['language'], '-') . '</div>';
                                break;
                            case 'packs_column_mobile' :
                                echo '<div class="col pl-1">' . Hash::get($product, 'play_description.mobile.price_description.' . $params['language'], '-') . '</div>';
                                break;
                            case 'packs_column_tv' :
                                echo '<div class="col pl-1">' . Hash::get($product, 'play_description.tv.price_description.' . $params['language'], '-') . '</div>';
                                break;
                        }
                    } ?>

				<?php } ?>
				<?php if(!$is_pack) { ?>
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

					<?php if(Hash::get($product, 'displayed_cashback', false)) { ?>
						<div class="mb-2 cursor-pointer">
							<?= Hash::get($product, 'displayed_cashback'); ?>
						</div>
					<?php } ?>

					<?= $product['order_button'] ?>
					<div class="font-s-08 mt-1">
						<?= $product['activation_price'] ?>
					</div>

				</div>
			</div>
		</article>

		<!-- MOBILE -->
		<article class="d-lg-none my-3 border text-<?= Hash::get($product, 'brand_slug') ?>-wrapper">
			<h3 class="font-weight-bold color-operator bg-lighter p-2 mb-2">
				<?php if ($params['version'] != 'cake') { ?>
				<a class="color-operator" href="<?= Hash::get($product, 'web.product_sheet_url.' . $params['language']) ?>">
					<?php } ?>
					<?php if(Hash::Get($params, 'options.display_operator_in_product_name', false)) {
						echo Hash::get($product, 'brand_name') . ' ';
					} ?>
					<?= Hash::get($product, 'short_name.' . $params['language']); ?>
					<?php if ($params['version'] != 'cake') { ?>
				</a>
			<?php } ?>
			</h3>
			<section class="row no-gutters px-2" class="text-left">
				<?php
				$n_col = ($is_pack !== false && count($col) > 1 ? '6' : '12');
				// Generate the specifics data columns
				foreach ($params['custom-col'] as $k => $col) { ?>
					<div class="col-<?=$n_col ?> <?= ($is_pack ? 'mb-2' : '' )?> mb-2">
						<?php if($is_pack) { ?>
							<div class="mb-1 font-weight-bold"><?= $col['name'] ?></div>
						<?php } ?>
						<?= $col['responsive_label'] ?>
						<?= self::getProductInfo($col['key_of_value'], $product, $params['version'], '_responsive'); ?>
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
				<div class="d-flex justify-content-around text-center">
					<?php if(Hash::get($product, 'displayed_cashback', false)) { ?>
						<div class="mb-2">
							<?php echo Hash::get($product, 'displayed_cashback'); ?>
						</div>
					<?php } ?>
					<div class="text-left pl-1">
						<?= $product['order_button'] ?>
						<?= $product['activation_price'] ?>
					</div>
				</div>
			</section>
		</article>
	<?php } ?>
</section>