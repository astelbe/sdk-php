# Astel API Client Library for PHP

It is a lightweight, Astel API client library for PHP. The SDK is best suited for implementing some functionnalities on your own. For example: telecom product catalog listing, options, joint offer for comparing, ordering and sales reporting. The whole Astel.be website is written using this SDK and the API as source of data.

You can either implement your own website using this SDK or use easily integrable [off-the-shelf tools - Web Integration Modules](https://github.com/astelbe/web-integration). These modules are injectable on any internet facing application or website by inserting some html tags in your own code.

## Astel API Documentation
Our swagger definition and documentation is available online: [SwaggerHub Astel Switch API V2_00](https://app.swaggerhub.com/apis/astel/switch/2_0).
You can directly test the API with your partner api key via swagger on clicking on the Authorize Button.

## Astel SDK Installation

[composer]: https://getcomposer.org

Install via [Composer][composer].

```
$ composer require astelbe/sdk-php
```
And use in your PHP code:
```php
require_once 'vendor/autoload.php';
```

or, simply, download an archive of our code and upload it on your application.

## Usage


See our developer site for more examples.

### Astel SDK Initialisation
```php
// All methods require authentication. To get your Astel API Private Token credentials, contact us

require_once 'vendor/autoload.php';

use AstelSDK\Model\Partner;
use AstelSDK\Model\Brand;

$envParticle = ''; // '' for production, 'sta' for staging env
$apiToken = '12345abcde'; // API Private Token provided by Astel
$isDebug = false; // For more debug info
$logPath = null; // Null for no logs, a valid writable path for file logs
$isPrivate = true; // Default private / professionnal param (Optionnal)
$language = 'FR'; // Default language used by the page (Optionnal)

$AstelApiContext = new AstelSDK\AstelContext($envParticle, $apiToken, $isDebug, $logPath);
$AstelApiContext->setIsPrivate($isPrivate);
$AstelApiContext->setLanguage($language);

// Utils debug function registering
AstelSDK\AstelContext::registerUtilsFunctions();
```
Now you are ready to call the API and retrieve data.

### Product Example
```php
$Product = AstelSDK\Model\Product::getInstance();

$products = $Product->find('all', [
	'_embed' => 'play_description,commission,web',
	'brand_id' => 3 // VOO
]);

debug($products);

$productVOOOne = $Product->find('first', [
	'id' => '1999', // VOO One
	'_embed' => 'play_description,commission,web',
]);

debug($productVOOOne);
```
It retrieves all VOO Products and their full description, the commission and cashback associated and web links for product page, and the second example retrieves a single product : Voo One.
 
### Discount Example:
```php
$Discount = Discount::getInstance();

$discounts = $Discount->find('all', [
	'brand_id' => 3,
	'_embed' => 'subscription_periods/product/commission,subscription_periods/product/web',
	'order' => '-weight',
	'count' => 50,
]);

debug($discounts);
```
It retrieves the 50 first active VOO discounts, order them by weight and retrives the associated recursive models: subscription_periods/product/commission and subscription_periods/product/web.

## Supported Platforms

* PHP 5.6 or higher


## How to contribute

All submissions are welcome. Fork the repository, read the rest of this README
file and make some changes. Once you're done with your changes send a pull
request. Thanks!


## Need Help? Found a bug?

[submitanissue]: https://github.com/astelbe/sdk-php/issues

Just [submit a issue][submitanissue] if you need any help. And, of course, feel
free to submit pull requests with bug fixes or changes.

Don't hesitate to contact Astel at direction@astel.be for more info or help for your integration.
