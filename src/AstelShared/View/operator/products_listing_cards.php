<?php

/**
 * TEMPLATE ASTEL
 * Used by astel.be
 */

use AstelShared\Translate\Translate;
use CakeUtility\Hash;
use AstelShared\SharedView;

$SharedView = SharedView::getInstance();

/*
  From operator pages public_html_v1/www/public_html/app/view/helper/ProductsListingCards.php
  From COMP in comparator-engine
  From ORDER form in mobile-options

  This listings is composed of cards. (productCard)
  Every cards is composed of one or more products (one in operator pages and more in comparator results) and a a card summary of the product(s)
  This template received $params :	array of productCards with products and pricings
  $params = [
        'title' => 'Title of the result',
        'id' => 'list_id', // for toggle details
        'product_link_in_new_tab' => true, // default false, to open product link in new tab
        'options' => [
            'display_operator_in_product_name' => true/false, // default true, to noyt display logo in operator page
        'productCards' => [
            0 =>[
                'products' => [
                    [
                        'brand_name' => 'Proximus',
                        'short_name' => 'Tuttimus',
                        '...' => ...,
                    ],
                ],
                'result_summary' => [
                    'order_url' => 'https://www.proximus.be',
                    'displayed_price' => 99,
                    'products_total_savings' => 99,
                    'setup' => '...',
                    'max_activation_time' => '...',
                    'phone_plug' => '...',
                    'total_cashback' => '...',
                    '...' => ...,
                ],
            ],
            ...
        ],
    ];
*/
//  debug($params);


/*
 * Handle bestSellers and allProducts urls with full param and tab hash support
 */

// Utilise l'URL complète réellement affichée dans le navigateur
$currentUrl = $_SERVER['REQUEST_URI']; // ex: /operateurs-disponibles/5500/dinant?id=648#nav-internet

// Optionnel : si tu veux un chemin absolu
$baseUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

// ID du tab actuel (fourni depuis $params ou extrait du hash)
$tabId = $params['id'] ?? '';

// Décomposition de l'URL
$urlParts = parse_url($currentUrl);
$path = $urlParts['path'] ?? '';
$query = [];
// Parse existing query parameters
if (isset($urlParts['query'])) {
  parse_str($urlParts['query'], $query);
}
// BESTSELLERS: remove 'display' from query
$bestsellerQuery = $query;
unset($bestsellerQuery['display']);
$bestsellersUrl = $path;
if (!empty($bestsellerQuery)) {
    $bestsellersUrl .= '?' . http_build_query($bestsellerQuery);
}
if (!empty($fragment)) {
    $bestsellersUrl .= '#nav-' . $fragment;
}

// ALL = tous les paramètres + display=all
$allQuery = $query;
$allQuery['display'] = 'all';
$allUrl = $path . '?' . http_build_query($allQuery);
if (!empty($fragment)) {
    $allUrl .= '#nav-' . $fragment;
}

// Exemple complet avec base
// $bestsellersUrl = $baseUrl . $bestsellersUrl;
// $allUrl = $baseUrl . $allUrl;

?>

<div class="container px-0 toggleProductListingDetails__container"
  id="toggleProductListingDetails__container_<?= $params['id'] ?>">
  <div
    class="d-xl-flex justify-content-between align-items-xl-center <?= (!empty($params['title']) ? ' bg-lightblue ' : '') ?> p-2 brad100 g100">
    <?php
    if (isset($params['title'])) {
    ?>
      <h2 class="m-0 fs125 align-content-center">
        <?= $params['title']; ?>
      </h2>
    <?php
    }
    ?>

    <div
      class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-center justify-content-sm-between w-100 w-sm-auto">
      <?php
      if ($params['display_best_seller_filter'] == 1) {
      ?>
        <div class="bestseller-filters d-md-flex g100 mr-0 mr-sm-4">
          <a href="<?= htmlspecialchars($bestsellersUrl); ?>"
            class="underlineWhenHovered <?= strpos($currentUrl, 'display=all') == false ? 'fw700' : 'fw400'; ?> text-darkblue text-nowrap mr-2"
            rel="nofollow">
            <?= Translate::get('ficheOperateur_display_bestsellers'); ?>
          </a>
          <a href="<?= htmlspecialchars($allUrl); ?>"
            class="underlineWhenHovered <?= strpos($currentUrl, 'display=all') !== false ? 'fw700' : 'fw400'; ?> text-darkblue text-nowrap"
            rel="nofollow">
            <?= Translate::get('ficheOperateur_display_all'); ?>
          </a>
        </div>
      <?php
      }
      ?>

      <div class="d-flex align-self-end align-items-center toggleProductListingDetails mt-sm-2 mt-lg-0 mt-2">
        <input type="checkbox" class="toggleProductListingDetails__button mr-2"
          id="toggle-product-listing-button-<?= $params['id'] ?>"
          onclick="toggleProductListingCards('<?= $params['id'] ?>')">
        <label for="toggle-product-listing-button-<?= $params['id'] ?>"
          class="m-0 toggleProductListingDetails__detailsLabel cursor-pointer">
          <?= Translate::get('switch_details'); ?>
        </label>
      </div>
    </div>
  </div>

  <div class="gridcontainer gridcontainer_listing g100 mt-4 mb-4" style="gap-row:1.2rem;">
    <?php
    // LOOP ON PRODUCT CARDS
    foreach ($params['productCards'] as $key => $result) {
      $cashback = ($result['result_summary']['total_cashback'] != '' && $result['result_summary']['total_cashback'] !== 0 && $result['cashback_source'] != 'None') ? $result['result_summary']['total_cashback'] : false;
      include __DIR__ . '/_product_card.php';
    } 
    ?>
  </div>
</div>
<!-- Modal Total Savings -->
<div class="modal fade" id="modalTotalSavings<?= $params['id'] ?>" tabindex="-1" role="dialog"
  aria-labelledby="modal total savings" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-title fs125 font-weight-bold text-darkblue">
          <?= Translate::get('total_savings_modal_title'); ?>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <?= Translate::get('total_savings_modal_content'); ?>
      </div>
    </div>
  </div>
</div>

<style>
  .product-card.position-relative.mb-3.current-highlight {
    background-color: #f5faff;
    border-radius: 1rem;
    border: 2px solid var(--blue);
  }

  .toggleProductListingDetails {
    width: fit-content;
    padding: 0.5rem;
    border-radius: 0.5rem;
  }

  .toggleProductListingDetails:has(input[type="checkbox"]:checked) {
    background-color: var(--darkblue);
    color: #fff;
  }

  .toggleProductListingDetails:has(input[type="checkbox"]:not(:checked)) {
    background-color: var(--lightblue);
    color: var(--darkblue);
    outline: 1px solid var(--blue);
  }

  .toggleProductListingDetails>input[type="checkbox"]:not(:checked) {
    outline: 1px solid var(--blue);
    background-color: #fff;
  }

  .toggleProductListingDetails>input[type="checkbox"] {
    appearance: none;
    height: 1.5rem;
    width: 1.5rem;
    padding: 0.25rem;
    border: none;
    border-radius: 25%;
    position: relative;
  }

  .toggleProductListingDetails>input[type="checkbox"]::after {
    display: block;
    position: absolute;
    content: "";
    background-color: var(--darkblue);
    transform: rotate(45deg);
    width: 0.5rem;
    height: 1rem;
    box-shadow: inset -0.125rem -0.125rem 0 0.1rem #fff;
    left: 0.5rem;
    transition: all 0.1s ease-in-out;
  }

  .toggleProductListingDetails>input[type="checkbox"]:not(:checked)::after {
    scale: 0;
  }

  .gridcontainer_listing {
    grid-template-columns: repeat(4, 1fr);
  }

  .information-box {
    box-shadow: 0 2px 30px 0 rgba(0, 0, 0, 0.1);
  }

  @media screen and (max-width: 1200px) {
    .gridcontainer_listing {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media screen and (max-width: 576px) {
    .gridcontainer_listing {
      grid-template-columns: 1fr;
    }
  }

  @media screen and (min-width: 576px) {
    .w-sm-auto {
      width: auto !important;
    }
  }

  @media screen and (max-width: 720px) {
    .information-box {
      border: 1px solid #dee2e6;
      box-shadow: 0 2px 30px 0 rgba(0, 0, 0, 0.3);
    }

    .bestseller-filters {
      margin-top: 1rem;
    }

    .mobile-underline {
      text-decoration: underline;
    }
  }
</style>