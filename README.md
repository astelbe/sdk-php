# Astel API Client Library for PHP

It is a lightweight, Astel API client library for PHP. 

## Astel API Documentation
Our swagger definition and documentation is available online: [SwaggerHub Astel Switch API V2_00](https://app.swaggerhub.com/apis/astel/switch/2_0).
You can directly test the API with your partner api key via swagger on clicking on the Authorize Button.

## Astel API Usage and Example (Postman Export)
A developer can also use Postman (https://www.getpostman.com/) to interact with the API. An export is [versioned in the export folder](https://github.com/astelbe/sdk-php/blob/master/export/Astel.postman_collection.json).

Import the JSON file in Postman and set the following variables:
 * {{env}}: Environment: '' for production, 'sta' for staging (test) environment
 * {{token}}: Astel Partner security Token
 * {{endpoint}}: set it to 'api'
 
 You are good to go and use our API. Some Usage Examples are available in the corresponding Postman folder.

 Some of the Endpoints uses an OPTIONS HTTP Method. Those are intended for querying extra information about the usage of the endpoint. If a developer wants to know the available query filters or orderings params, he can query those OPTIONS endpoints.

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
```
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
```
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
