<?php

// require
require_once 'config.php';
require_once '../bpost.php';

// create instance
$bpost = new bPost(ACCOUNT_ID, PASSPHRASE);

// create notification
//$option =  new bPostNotification('NL', 'tijs@verkoyen.eu');

// create delivery method at home
//$deliveryMethod = new bPostDeliveryMethodAtHome();
//$deliveryMethod->setNormal();
//$deliveryMethod->setNormal(
//	array(
//		'infoDistributed' => $option,
//		'infoNextDay' => $option,
//		'infoReminder' => $option,
//		'automaticSecondPresentation' => '',
//	)
//);
//$deliveryMethod->setDropAtTheDoor();
//$deliveryMethod->setInsurance(10);

// create delivery method at shop
//$deliveryMethod = new bPostDeliveryMethodAtShop();
//$deliveryMethod->setInfoPugo(1, '1', $option);
//$deliveryMethod->setInfoDistributed(new bPostNotification('NL', 'foo@bar.com'));
//$deliveryMethod->setInsurance(10);

// create delivery method at 24-7
//$deliveryMethod = new bPostDeliveryMethodAt247('014473', '123456789');
//$deliveryMethod->setInsurance(10);
//$deliveryMethod->setSignature(true);

// create delivery method int business
//$deliveryMethod = new bPostDeliveryMethodIntBusiness();
//$deliveryMethod->setInsurance(10);

// create delivery method int express
//$deliveryMethod = new bPostDeliveryMethodIntExpress();

// create address
//$deliveryAddress = new bPostAddress('Kerkstraat', '108', '9050', 'Gentbrugge');
//$deliveryAddress = new bPostAddress('Dieselstr.', '24', '85748', 'Garching', 'DE');

// create customer
//$customer = new bPostCustomer('Tijs', 'Verkoyen');
//$customer->setDeliveryAddress($deliveryAddress);

$orderId = time();

// create order
//$order = new bPostOrder($orderId);
//$order->setStatus('OPEN');
//$order->setCostCenter('Vitashop');
//$order->addOrderLine('Item 1', 10);
//$order->addOrderLine('Item 2', 20);
//$order->setCustomer($customer);
//$order->setDeliveryMethod($deliveryMethod);
//$order->setTotal(100);

//$labelInfo1 = new bPostInternationalLabelInfo(100, 300, 'Something', 'OTHER', 'RTA', true);
//$labelInfo2 = new bPostInternationalLabelInfo(200, 400, 'Something else', 'GIFT', 'ABANDONED', false);

//$response = $bpost->createOrReplaceOrder($order);
//$response = $bpost->fetchOrder($orderId);
//$response = $bpost->modifyOrderStatus(660, 'OPEN');
//$response = $bpost->createNationalLabel($orderId, 1);
//$response = $bpost->createInternationalLabel($orderId, array($labelInfo1, $labelInfo2), true);
//$response = $bpost->createOrderAndNationalLabel($order, 5);
//$response = $bpost->createOrderAndInternationalLabel(array($labelInfo1, $labelInfo2), $order);
//$response = $bpost->retrievePDFLabelsForBox($response['barcode'][0]);
//$response = $bpost->retrievePDFLabelsForOrder($orderId);

// output pdf
//header('Content-Type: application/pdf');
//echo base64_decode($response);
//exit;

// output
var_dump($response);