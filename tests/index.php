<?php

// require
require_once 'config.php';
require_once '../bpost.php';

// create instance
$bpost = new bPost(ACCOUNT_ID, PASSPHRASE);

// $response = $bpost->fetchOrder(660);
// $response = $bpost->modifyOrderStatus(660, 'OPEN');
// $response = $bpost->createNationalLabel(660, 8, true, true);

// output (Spoon::dump())
ob_start();
var_dump($response);
$output = ob_get_clean();

// cleanup the output
$output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);

// print
echo '<pre>' . htmlspecialchars($output, ENT_QUOTES, 'UTF-8') . '</pre>';