# bpost API client

[![Build Status](https://scrutinizer-ci.com/g/Antidot-be/bpost-api-library/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Antidot-be/bpost-api-library)
[![Latest Stable Version](https://poser.pugx.org/antidot-be/bpost-api-library/v/stable)](https://packagist.org/packages/antidot-be/bpost-api-library)
[![Latest Unstable Version](https://poser.pugx.org/antidot-be/bpost-api-library/v/unstable)](https://packagist.org/packages/antidot-be/bpost-api-library)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Antidot-be/bpost-api-library/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Antidot-be/bpost-api-library)
[![Code Coverage](https://scrutinizer-ci.com/g/Antidot-be/bpost-api-library/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Antidot-be/bpost-api-library)
[![Total Downloads](https://poser.pugx.org/antidot-be/bpost-api-library/downloads)](https://packagist.org/packages/antidot-be/bpost-api-library)
[![License](https://poser.pugx.org/antidot-be/bpost-api-library/license)](https://packagist.org/packages/antidot-be/bpost-api-library)

## About

_bpost API library_ is a PHP library which permit to your PHP application to communicate with the [bpost API](http://bpost.be).

## Installation

```bash
composer install antidot-be/bpost-api-library
```

## Usages

### Orders

#### Common objects

```php

/* Call the Composer autoloader */
require '../vendor/autoload.php';

use Bpost\BpostApiClient\Bpost;
use Bpost\BpostApiClient\Bpost\Order;
use Bpost\BpostApiClient\Bpost\Order\Address;
use Bpost\BpostApiClient\Bpost\Order\Box;
use Bpost\BpostApiClient\Bpost\Order\Box\AtBpost;
use Bpost\BpostApiClient\Bpost\Order\Box\AtHome;
use Bpost\BpostApiClient\Bpost\Order\Box\Customsinfo\CustomsInfo;
use Bpost\BpostApiClient\Bpost\Order\Box\International;
use Bpost\BpostApiClient\Bpost\Order\Box\Option\Insurance;
use Bpost\BpostApiClient\Bpost\Order\Box\Option\Messaging;
use Bpost\BpostApiClient\Bpost\Order\Line;
use Bpost\BpostApiClient\Bpost\Order\PugoAddress;
use Bpost\BpostApiClient\Bpost\Order\Receiver;
use Bpost\BpostApiClient\Bpost\ProductConfiguration\Product;
use Bpost\BpostApiClient\BpostException;
use Psr\Log\LoggerInterface;


$apiUrl = "https://api.bpost.be/services/shm/";
$apiUsername = "107423";
$apiPassword = "MyGreatApiPassword";

$bpost = new Bpost($apiUsername, $apiPassword, $apiUrl);

/* We set the receiver postal address, without the name */
$receiverAddress = new Address();
$receiverAddress->setStreetName("Rue du Grand Duc");
$receiverAddress->setNumber(13);
$receiverAddress->setPostalCode(1040);
$receiverAddress->setLocality("Etterbeek");
$receiverAddress->setCountryCode("BE"); // ISO2

/* We set the receiver postal address, without the name */
$receiver = new Receiver();
$receiver->setAddress($receiverAddress);
$receiver->setName("Alma van Appel");
$receiver->setPhoneNumber("+32 2 641 13 90);
$receiver->setEmailAddress("alma@antidot.com");

$orderReference = "ref_0123456789"; // An unique order reference
$order = new Order($orderReference);

/**
 * A order line is an order item, like a article
 */
$order->addLine(
    new Line("Article description", 1)
);
$order->addLine(
    new Line("Some others articles", 5)
);

/**
 * A box is used to split your shipping in many packages
 * The box weight must be littlest than to 30kg
 */
$box = new Box();

/**
 * Available boxes for national box:
 * - AtHome: Delivered at the given address
 * - AtBpost: Delivered in a bpost office
 * - BpostOnAppointment: Delivered in a shop
 *
 * Available boxes for international box:
 * - International: Delivered at the given address
 */
$atHome = new AtHome();
$atHome->setProduct(Product::PRODUCT_NAME_BPACK_24H_BUSINESS);
$atHome->setReceiver($receiver);

/* Add options */
$atHome->addOption(
    new Insurance(
        Insurance::INSURANCE_TYPE_ADDITIONAL_INSURANCE,
        Insurance::INSURANCE_AMOUNT_UP_TO_2500_EUROS
    )
);

$box->setNationalBox($atHome);

$order->addBox($box);
```

#### Create an order

We use the variables set before.

```php
$bpost->createOrReplaceOrder($order); // The order is created with status Box::BOX_STATUS_PENDING
```

#### Update order status

```php
$bpost->modifyOrderStatus($orderReference, Box::BOX_STATUS_OPEN);
```

#### Get order info

```php
$order = $bpost->fetchOrder($orderReference);

$boxes = $order->getBoxes();
$lines = $order->getLines();
```

### Labels

#### Get labels from an order

```php
$labels = $bpost->createLabelForOrder(
    $orderReference,
    Bpost::LABEL_FORMAT_A6, // $format
    false, // $withReturnLabels
    true // $asPdf
);
foreach ($labels as $label) {
    $barcode = $label->getBarcode();
    $mimeType = $label->getMimeType(); // Label::LABEL_MIME_TYPE_*
    $bytes = $label->getBytes();
    file_put_contents("$barcode.pdf", $bytes);
}
```

#### Get labels from an existing barcode

```php
$labels = $bpost->createLabelForOrder(
    $boxBarcode,
    Bpost::LABEL_FORMAT_A6, // $format
    false, // $withReturnLabels
    true // $asPdf
);
foreach ($labels as $label) {
    $barcode = $label->getBarcode(); // Can be different than $boxBarcode if this is a return label
    $mimeType = $label->getMimeType(); // Label::LABEL_MIME_TYPE_*
    $bytes = $label->getBytes();
    file_put_contents("$barcode.pdf", $bytes);
}
```

#### Get labels from an existing barcode

```php
$labels = $bpost->createLabelInBulkForOrders(
    array(
        $orderReference1,
        $orderReference2,
    ),
    Bpost::LABEL_FORMAT_A6, // $format
    false, // $withReturnLabels
    true // $asPdf
);
foreach ($labels as $label) {
    $barcode = $label->getBarcode(); // Can be different than $boxBarcode if this is a return label
    $mimeType = $label->getMimeType(); // Label::LABEL_MIME_TYPE_*
    $bytes = $label->getBytes();
    file_put_contents("$barcode.pdf", $bytes);
}
```

### Geo6 services

```php
$geo6Partner = '999999';
$geo6AppId = 'A001';
$geo6 = new Geo6($geo6Partner, $geo6AppId);
```

#### Get nearest points
```php
$points = $geo6->getNearestServicePoint(
    'Grand Place', // Street name
    '3', // Street number
    '1000', // Zip code
    'fr', // Language: 'fr' or 'nl'
    3, // Point types: Sum of some Geo6::POINT_TYPE_*
    5 // Points number
);
foreach ($points as $point) {
    $distance = $point['distance']; // float
    /** @var Poi $poi */
    $poi = $point['poi'];
}
```

#### Get point details
```php
/** @var Poi $poi */
$poi = $geo6->getServicePointDetails(
    200000, // Point ID
    'fr', // Language: 'fr' or 'nl'
    3 // Point types: Sum of some Geo6::POINT_TYPE_*
);
```

#### Get point map URL
```php
$url = $geo6->getServicePointPageUrl(
    200000, // Point ID
    'fr', // Language: 'fr' or 'nl'
    3 // Point types: Sum of some Geo6::POINT_TYPE_*
);
```

## Sites using this class

* [Each site based on the Web Retail Shop Platform](http://www.webretailcompany.be)
* [The bpost plugin for WordPress](https://wordpress.org/plugins/bpost-shipping)

## Would like contribute ?

You can read the [CONTRIBUTING.md](https://github.com/Antidot-be/bpost-api-library/blob/master/CONTRIBUTING.md) file
