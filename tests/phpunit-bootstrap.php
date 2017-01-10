<?php
include_once __DIR__ . "/../vendor/autoload.php";

// To define ACCOUNT_ID and PASSPHRASE constants for the API connection (connection-test folder):
$credentialFile = __DIR__ . "/phpunit-credentials.php";
if (file_exists($credentialFile)) {
    include_once $credentialFile;
} else {
    include_once $credentialFile . '.dist';
}

define('BPACK_EMAIL', 'toto@mail.com');
define('BPACK_PASSPHRASE', 'toto');
define('GEO6_PARTNER', '999999');
define('GEO6_APP_ID', 'A001');
