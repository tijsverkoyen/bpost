<?php

//require
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/config.php';

use \TijsVerkoyen\Bpost\Bpost;

use \TijsVerkoyen\Bpost\Bpost\Order;
use \TijsVerkoyen\Bpost\Bpost\Order\Address;
use \TijsVerkoyen\Bpost\Bpost\Order\Box;
use \TijsVerkoyen\Bpost\Bpost\Order\Box\AtHome;
use \TijsVerkoyen\Bpost\Bpost\Order\Box\AtBpost;
use \TijsVerkoyen\Bpost\Bpost\Order\Box\At247;
use \TijsVerkoyen\Bpost\Bpost\Order\Box\Customsinfo\CustomsInfo;
use \TijsVerkoyen\Bpost\Bpost\Order\Box\International;
use \TijsVerkoyen\Bpost\Bpost\Order\Box\Option\AutomaticSecondPresentation;
use \TijsVerkoyen\Bpost\Bpost\Order\Box\Option\CashOnDelivery;
use \TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Insurance;
use \TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Messaging;
use \TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Signature;
use \TijsVerkoyen\Bpost\Bpost\Order\Box\Openinghour\Day as OpeninghourDay;
use \TijsVerkoyen\Bpost\Bpost\Order\Line as OrderLine;
use \TijsVerkoyen\Bpost\Bpost\Order\Receiver;
use \TijsVerkoyen\Bpost\Bpost\Order\Sender;
use \TijsVerkoyen\Bpost\Bpost\Order\ParcelsDepotAddress;
use \TijsVerkoyen\Bpost\Bpost\Order\PugoAddress;

use \TijsVerkoyen\Bpost\Geo6;

use \TijsVerkoyen\Bpost\FormHandler;

use \TijsVerkoyen\Bpost\Bpack247;

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
$sender->setEmailAddress('bpost.sender@verkoyen.eu');

$box = new Box();
$box->setSender($sender);
$box->setRemark('Remark');

// add label
$address = new Address();
$address->setStreetName('Kerkstraat');
$address->setNumber('108');
$address->setPostalCode('9050');
$address->setLocality('Gentbrugge');
$address->setCountryCode('BE');

$receiver = new Receiver();
$receiver->setAddress($address);
$receiver->setName('Tijs Verkoyen');
$receiver->setCompany('Sumo Coders');
$receiver->setPhoneNumber('+32 9 395 02 51');
$receiver->setEmailAddress('bpost.receiver@verkoyen.eu');

// options
$option = new Messaging('infoDistributed', 'NL', 'bpost@verkoyen.eu');
//$option = new Messaging('infoNextDay', 'NL', 'bpost@verkoyen.eu');
//$option = new Messaging('infoReminder', 'NL', 'bpost@verkoyen.eu');
//$option = new Messaging('keepMeInformed', 'NL', 'bpost@verkoyen.eu');
//$option = new CashOnDelivery(
//    1251,
//    'BE19210023508812',
//    'GEBABEBB'
//);
//$option = new Signature();
//$option = new Insurance('additionalInsurance', 3);
//$option = new AutomaticSecondPresentation();

// @Home
$atHome = new AtHome();
$atHome->setProduct('bpack 24h Pro');
$atHome->setWeight(2000);
$atHome->setReceiver($receiver);
$atHome->addOption($option);
$box->setNationalBox($atHome);

// @Bpost
$pugoAddress = new PugoAddress(
    'Turnhoutsebaan',
    '468',
    null,
    '2110',
    'Wijnegem',
    'BE'
);

$atBpost = new AtBpost();
$atBpost->setWeight(2000);
$atBpost->setPugoId('207500');
$atBpost->setPugoName('WIJNEGEM');
$atBpost->setPugoAddress($pugoAddress);
$atBpost->setReceiverName('Tijs Verkoyen');
$atBpost->setReceiverCompany('Sumo Coders');
//$box->setNationalBox($atBpost);

// @24/7
$parcelsDepotAddress = new ParcelsDepotAddress(
    'Turnhoutsebaan',
    '468',
    null,
    '2110',
    'Wijnegem',
    'BE'
);
$parcelsDepotAddress->setBox('A');

$at247 = new At247();
$at247->setParcelsDepotId('014472');
$at247->setParcelsDepotName('WIJNEGEM');
$at247->setParcelsDepotAddress($parcelsDepotAddress);
$at247->setMemberId('188565346');
$at247->setReceiverName('Tijs Verkoyen');
$at247->setReceiverCompany('Sumo Coders');
//$box->setNationalBox($at247);

// international
$customsInfo = new CustomsInfo();
$customsInfo->setParcelValue(700);
$customsInfo->setContentDescription('BOOK');
$customsInfo->setShipmentType('DOCUMENTS');
$customsInfo->setParcelReturnInstructions('RTS');
$customsInfo->setPrivateAddress(false);

$international = new International();
$international->setProduct('bpack World Express Pro');
$international->setReceiver($receiver);
$international->setParcelWeight(2000);
$international->setCustomsInfo($customsInfo);
//$box->setInternationalBox($international);

$order->addBox($box);

try {
    // Bpost webservices
//    $response = $bpost->createOrReplaceOrder($order);
//    $response = $bpost->modifyOrderStatus($orderId, 'OPEN');
//    $response = $bpost->fetchOrder($orderId);
//    $response = $bpost->createLabelForOrder('1398779096', 'A4');
//    $response = $bpost->createLabelForBox('323212345659900357664050', 'A4');
//    $response = $bpost->createLabelInBulkForOrders(
//        array('1398779096', '1398862819'), 'A4'
//    );

    // GEO6 webservices
//    $geo6 = new Geo6(GEO6_PARTNER, GEO6_APP_ID);
//    $response = $geo6->getNearestServicePoint('Afrikalaan', '289', '9000', 'nl', 7, 100);
//    $response = $geo6->getServicePointDetails('220000', 'nl', '1');
//    $response = $geo6->getServicePointPage('220000', 'nl', '1');

    // Bpack 24/7 webservices
//    $bpack247 = new Bpack247(BPACK_EMAIL, BPACK_PASSPHRASE);
//    $response = $bpack247->getMember('344337728');

//    $customer = new Bpack247\Customer();
//    $customer->setFirstName('Tijs');
//    $customer->setLastName('Verkoyen');
//    $customer->setEmail('bpost@verkoyen.eu');
//    $customer->setStreet('Afrikalaan');
//    $customer->setNumber('289');
//    $customer->setMobileNumber('123456');
//    $customer->setPostalCode('9000');
//    $customer->setPreferredLanguage('nl-BE');
//    $customer->setTitle('Mr.');
//
//    $response = $bpack247->createMember($customer);

    // Form handler
//    $formHandler = new FormHandler(ACCOUNT_ID, PASSPHRASE);
//    $formHandler->setParameter('action', 'START');
//    $formHandler->setParameter('orderReference', $order->getReference());
//    $formHandler->setParameter('customerCountry', $sender->getAddress()->getCountryCode());
//    $response = $formHandler->getParameters(true);
} catch (Exception $e) {
    var_dump($e);
}

// output
var_dump($response);
