<?php

// require
require_once 'config.php';
require_once '../bpost.php';

// create instance
$bpost = new bPost(ACCOUNT_ID, PASSPHRASE);

// create notification
$option =  new bPostNotification('NL', 'tijs@verkoyen.eu');

// create delivery method
$deliveryMethod = new bPostDeliveryMethodAtHome();
$deliveryMethod->setNormal(
	array(
		'infoDistributed' => $option,
		'infoNextDay' => $option,
		'infoReminder' => $option,
		'automaticSecondPresentation' => '',
	)
);
//$deliveryMethod->setInsurance(10);

// create address
$deliveryAddress = new bPostAddress('Kerkstraat', '108', '9050', 'Gentbrugge');

// create customer
$customer = new bPostCustomer('Tijs', 'Verkoyen');
$customer->setDeliveryAddress($deliveryAddress);

$orderId = time();

// create order
$order = new bPostOrder($orderId, 'OPEN');
$order->setStatus('OPEN');
$order->setCostCenter('Vitashop');
$order->addOrderLine('Item 1', 10);
$order->addOrderLine('Item 2', 20);
$order->setCustomer($customer);
$order->setDeliveryMethod($deliveryMethod);
$order->setTotal(100);

//$response = $bpost->createOrReplaceOrder($order);
//$response = $bpost->fetchOrder($orderId);
//$response = $bpost->modifyOrderStatus(660, 'OPEN');
//$response = $bpost->createrNationalLabel($orderId, 1);
//$response = $bpost->createOrderAndNationalLabel($order, 5);
//$response = $bpost->retrievePDFLabelsForBox($response['barcode'][0]);
//$response = $bpost->retrievePDFLabelsForOrder($orderId);

// output pdf
//header('Content-Type: application/pdf');
//echo base64_decode($response);
//exit;

// output (Spoon::dump())
ob_start();
var_dump($response);
$output = ob_get_clean();

// cleanup the output
$output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);

// print
echo '<pre>' . htmlspecialchars($output, ENT_QUOTES, 'UTF-8') . '</pre>';