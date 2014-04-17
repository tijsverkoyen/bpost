<?php

//require
require_once '../../../autoload.php';
require_once 'config.php';

use \TijsVerkoyen\Bpost\Bpost;
use \TijsVerkoyen\Bpost\Notification;
use \TijsVerkoyen\Bpost\DeliveryMethodAtHome;
use \TijsVerkoyen\Bpost\DeliveryMethodAtShop;
use \TijsVerkoyen\Bpost\DeliveryMethodAt247;
use \TijsVerkoyen\Bpost\DeliveryMethodIntBusiness;
use \TijsVerkoyen\Bpost\DeliveryMethodIntExpress;
use \TijsVerkoyen\Bpost\Address;
use \TijsVerkoyen\Bpost\Customer;
use \TijsVerkoyen\Bpost\Order;
use \TijsVerkoyen\Bpost\InternationalLabelInfo;

use \TijsVerkoyen\Bpost\Geo6;

// create instance
$bpost = new Bpost(ACCOUNT_ID, PASSPHRASE);

// create notification
//$option =  new Notification('NL', 'tijs@verkoyen.eu');

// create delivery method at home
//$deliveryMethod = new DeliveryMethodAtHome();
//$deliveryMethod->setNormal(
//    array(
//        'infoDistributed' => $option,
//        'infoNextDay' => $option,
//        'infoReminder' => $option,
//        'automaticSecondPresentation' => '',
//    )
//);
//$deliveryMethod->setDropAtTheDoor();
//$deliveryMethod->setInsurance(10);

//// create delivery method at shop
//$deliveryMethod = new DeliveryMethodAtShop();
//$deliveryMethod->setInfoPugo(1, '1', $option);
//$deliveryMethod->setInfoDistributed(new Notification('NL', 'foo@bar.com'));
//$deliveryMethod->setInsurance(10);
//
//// create delivery method at 24-7
//$deliveryMethod = new DeliveryMethodAt247('014473', '123456789');
//$deliveryMethod->setInsurance(10);
//$deliveryMethod->setSignature(true);
//
//// create delivery method int business
//$deliveryMethod = new DeliveryMethodIntBusiness();
//$deliveryMethod->setInsurance(10);
//
//// create delivery method int express
//$deliveryMethod = new DeliveryMethodIntExpress();

// create address
//$deliveryAddress = new Address('Kerkstraat', '108', '9050', 'Gentbrugge');
//$deliveryAddress = new Address('Dieselstr.', '24', '85748', 'Garching', 'DE');

// create customer
//$customer = new Customer('Tijs', 'Verkoyen');
//$customer->setDeliveryAddress($deliveryAddress);

//$orderId = time();

// create order
//$order = new Order($orderId);
//$order->setStatus('OPEN');
//$order->setCostCenter('Vitashop');
//$order->addOrderLine('Item 1', 10);
//$order->addOrderLine('Item 2', 20);
//$order->setCustomer($customer);
//$order->setDeliveryMethod($deliveryMethod);
//$order->setTotal(100);

//$labelInfo1 = new InternationalLabelInfo(100, 300, 'Something', 'OTHER', 'RTA', true);
//$labelInfo2 = new InternationalLabelInfo(200, 400, 'Something else', 'GIFT', 'ABANDONED', false);


try {
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
