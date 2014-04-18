<?php

//require
require_once '../../../autoload.php';
require_once 'config.php';

use \TijsVerkoyen\Bpost\Bpost;
use \TijsVerkoyen\Bpost\Bpost\Order;
use \TijsVerkoyen\Bpost\Bpost\Order\Box;
use \TijsVerkoyen\Bpost\Bpost\Order\Line as OrderLine;
use \TijsVerkoyen\Bpost\Bpost\Order\Sender;
use \TijsVerkoyen\Bpost\Bpost\Order\Address;

use \TijsVerkoyen\Bpost\Geo6;

// create instance
$bpost = new Bpost(ACCOUNT_ID, PASSPHRASE);

// create order
$orderId = time();
$order = new Order($orderId);
$order->setCostCenter('Cost Center');

// add lines
$line1 = new OrderLine('Beer', 1);
$order->addLine($line1);
$line2 = new OrderLine('Whisky', 100);
$order->addLine($line2);

// add box
$address = new Address();
$address->setStreetName('Afrikalaan');
$address->setNumber('289');
$address->setPostalCode('9000');
$address->setLocality('Gent');
$address->setCountryCode('BE');

$sender = new Sender();
$sender->setAddress($address);
$sender->setName('Tijs Verkoyen');
$sender->setCompany('Sumo Coders');
$sender->setPhoneNumber('+32 9 395 02 51');
$sender->setEmailAddress('bpost@verkoyen.eu');

$box = new Box();
$box->setSender($sender);
$box->setRemark('Remark');

$order->addBox($box);

try {
    // Bpost webservices
//    $response = $bpost->createOrReplaceOrder($order);
//    $response = $bpost->fetchOrder($orderId);
//    $response = $bpost->modifyOrderStatus(660, 'OPEN');
//    $response = $bpost->createNationalLabel($orderId, 1);
//    $response = $bpost->createInternationalLabel($orderId, array($labelInfo1, $labelInfo2), true);
//    $response = $bpost->createOrderAndNationalLabel($order, 5);
//    $response = $bpost->createOrderAndInternationalLabel(array($labelInfo1, $labelInfo2), $order);
//    $response = $bpost->retrievePDFLabelsForBox($response['barcode'][0]);
//    $response = $bpost->retrievePDFLabelsForOrder($orderId);

    // GEO6 webservices
//    $geo6 = new Geo6(GEO6_PARTNER, GEO6_APP_ID);
//    $response = $geo6->getNearestServicePoint('Afrikalaan', '289', '9000', 'nl', 7, 100);
//    $response = $geo6->getServicePointDetails('220000', 'nl', '1');
//    $response = $geo6->getServicePointPage('220000', 'nl', '1');
} catch (Exception $e) {
    var_dump($e);
}

// output pdf
//header('Content-Type: application/pdf');
//echo base64_decode($response);
//exit;

// output
var_dump($response);
