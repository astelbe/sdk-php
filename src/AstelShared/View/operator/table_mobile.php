<?php
use CakeUtility\Hash;
$is_pack = $params['play_type'] === 'packs';
?>

<section class="operator-products-table mb-5">
	<?php if(Hash::get($params, 'title', false)) { ?>
		<h2 class="mt-3 mb-2">
			<span class="d-inline-block pr-2">
				<?= $params['title']; ?>
			</span>
            <span class="d-inline-block pl-3"><?= $params['fa-icon'] ?></span>
		</h2>
	<?php } ?>

	<header class="row d-none my-1 no-gutters d-lg-flex align-items-end text-center border-bottom border-blue text-blue font-weight-bold"
			style="font-size:0.7rem;">
		<div class="col-lg-2 text-left">
			<?= $params['col-headers']['product'] ?>
		</div>
		<?php
		// Generate the specifics data columns
		foreach ($params['custom-col'] as $col) { ?>
			<div class="col<?= ($is_pack ? ' pl-1 text-left' : '') ?>">
				<?php if ($col['name'] == 'download_speed' && $params['language'] == 'NL') { ?>
					<div style="margin-top:-31px;">
						<?= $col['name'] ?>
					</div>
				<?php } else { ?>
					<?= $col['name'] ?>
				<?php } ?>
			</div>
		<?php } ?>
        <?php if(!$is_pack) { ?>
            <div class="col-lg-2">
                <?= $params['col-headers']['bonus'] ?>
            </div>
        <?php } ?>
		<div class="col-lg-2">
			<?= $params['col-headers']['pack_price'] ?>
		</div>
		<div class="col-lg-3">
			<?= $params['col-headers']['cashback_and_order'] ?>
		</div>
	</header>
	
	<?php foreach ($params['products'] as $k => $product) { ?>
		<!-- DESKTOP -->
		<article class="row d-none d-lg-flex my-2 no-gutters align-items-start text-center border-bottom text-<?= Hash::get($product, 'brand_slug') ?>-wrapper">
			<div class="col-lg-2 text-left">
				<h3 class="mb-0 font-weight-bold font-s-1">
					<a class="color-operator text-<?= Hash::get($product, 'brand_slug') ?>" href="<?= Hash::get($product, 'web.product_sheet_url.' . $params['language']) ?>">
                        <?php if(Hash::Get($params, 'options.display_operator_in_product_name', false)) {
                            echo Hash::get($product, 'brand_name') . ' ';
                        } ?>
						<?php
						echo Hash::get($product, 'short_name.' . $params['language']);
						?>
					</a>
				</h3>
			</div>
			<?php
			// Generate the 3 specifics data columns
			foreach ($params['custom-col'] as $col) {
                ?>
				<div class="col<?= ($is_pack ? ' pl-1 text-left' : '') ?>">
                    <?php if($is_pack) { ?>
                        <b><?= $col['responsive_label'] ?> </b>
                    <?php } ?>
                    <?= self::getProductInfo($col['key_of_value'], $product, $params['version'], '_responsive'); ?>
				</div>
			<?php } ?>
            <?php if(!$is_pack) { ?>
                <div class="col-lg-2">
                    <?php
                    $price_description = Hash::get($product, 'play_description.' . $params['play_type'] . '.price_description.' . $params['language']);
                    if ($price_description) {
                        echo $price_description;
                    }
                    ?>
                </div>
            <?php } ?>
			<div class="col-lg-2">
				<?= $product['displayed_price'] ?>
			</div>
			<div class="col-lg-3 mt-2 text-center">

				<div class="mb-2 cursor-pointer" data-toggle="modal" data-target="#modalExplainCashback">
					<?php
					if(Hash::get($product, 'displayed_cashback', false)) {
						echo Hash::get($product, 'displayed_cashback');
					}
					?>
				</div>

				<?= $product['order_button'] ?>
				<?= $product['activation_price'] ?>

			</div>
		</article>

		<!-- MOBILE -->
		<article class="row d-flex d-lg-none my-2 border-bottom pb-3 text-<?= Hash::get($product, 'brand_slug') ?>-wrapper">
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
					<div class="<?= ($is_pack ? 'mb-2' : '' )?>">
                        <?php if($is_pack) { ?>
                            <div class="mb-1 font-weight-bold"><?= $col['name'] ?></div>
                            <b><?= $col['responsive_label'] ?> </b>
                        <?php } ?>
                        <?= self::getProductInfo($col['key_of_value'], $product, $params['version'], '_responsive'); ?>
					</div>
				<?php } ?>
				
				<?php
				$price_description = Hash::get($product, 'play_description.' . $play_type . '.price_description.' . $params['language']);
				if ($price_description) {
					echo '<p class="mt-2">' . $price_description . '</p>';
				}
				?>
			</section>
			<section class="col-6 text-center">
				<div class="font-s-11">
					<?= $product['displayed_price'] ?>
				</div>
				<div class="text-center">
					<div class="mb-2" data-toggle="modal" data-target="#modalExplainCashback">
						<?php
						if(Hash::get($product, 'displayed_cashback', false)) {
							echo Hash::get($product, 'displayed_cashback');
						}
						?>
					</div>

					<?= $product['order_button'] ?>
					<?= $product['activation_price'] ?>
				</div>
			</section>
		</article>
	<?php } ?>
</section>