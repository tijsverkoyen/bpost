<?php
namespace TijsVerkoyen\Bpost;

/**
 * bPost class
 *
 * This source file can be used to communicate with the bPost Shipping Manager API
 *
 * The class is documented in the file itself. If you find any bugs help me out and report them. Reporting can be done by sending an email to php-bpost-bugs[at]verkoyen[dot]eu.
 * If you report a bug, make sure you give me enough information (include your code).
 *
 * Changelog since 1.0.1
 * - better errorhandling
 *
 * Changelog since 1.0.0
 * - added a class to handle the form.
 *
 * License
 * Copyright (c), Tijs Verkoyen. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * This software is provided by the author "as is" and any express or implied warranties, including, but not limited to, the implied warranties of merchantability and fitness for a particular purpose are disclaimed. In no event shall the author be liable for any direct, indirect, incidental, special, exemplary, or consequential damages (including, but not limited to, procurement of substitute goods or services; loss of use, data, or profits; or business interruption) however caused and on any theory of liability, whether in contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of this software, even if advised of the possibility of such damage.
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version 1.0.1
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license BSD License
 */
class Bpost
{
    // internal constant to enable/disable debugging
    const DEBUG = false;

    // URL for the api
    const API_URL = 'https://api.bpost.be/services/shm';

    // current version
    const VERSION = '1.0.1';

    /**
     * The account id
     *
     * @var string
     */
    private $accountId;

    /**
     * A cURL instance
     *
     * @var resource
     */
    private $curl;

    /**
     * The passPhrase
     *
     * @var string
     */
    private $passPhrase;

    /**
     * The port to use.
     *
     * @var int
     */
    private $port;

    /**
     * The timeout
     *
     * @var int
     */
    private $timeOut = 10;

    /**
     * The user agent
     *
     * @var string
     */
    private $userAgent;

    // class methods
    /**
     * Create bPost instance
     *
     * @param string $accountId
     * @param string $passPhrase
     */
    public function __construct($accountId, $passPhrase)
    {
        $this->accountId = (string) $accountId;
        $this->passPhrase = (string) $passPhrase;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        // is the connection open?
        if ($this->curl !== null) {
            // close connection
            curl_close($this->curl);

            // reset
            $this->curl = null;
        }
    }

    /**
     * Callback-method for elements in the return-array
     *
     * @param mixed       $input The value.
     * @param string      $key   string	The key.
     * @param DOMDocument $xml   Some data.
     */
    private static function arrayToXML(&$input, $key, $xml)
    {
        // wierd stuff
        if (in_array($key, array('orderLine', 'internationalLabelInfo'))) {
            foreach ($input as $row) {
                $element = new \DOMElement($key);
                $xml->appendChild($element);

                // loop properties
                foreach ($row as $name => $value) {
                    if(is_bool($value)) $value = ($value) ? 'true' : 'false';

                    $node = new \DOMElement($name, $value);
                    $element->appendChild($node);
                }
            }

            return;
        }

        // skip attributes
        if($key == '@attributes') return;

        // create element
        $element = new \DOMElement($key);

        // append
        $xml->appendChild($element);

        // no value? just stop here
        if($input === null) return;

        // is it an array and are there attributes
        if (is_array($input) && isset($input['@attributes'])) {
            // loop attributes
            foreach((array) $input['@attributes'] as $name => $value) $element->setAttribute($name, $value);

            // reset value
            if(count($input) == 2 && isset($input['value'])) $input = $input['value'];

            // reset the input if it is a single value
            elseif(count($input) == 1) return;
        }

        // the input isn't an array
        if (!is_array($input)) {
            // boolean
            if(is_bool($input)) $element->appendChild(new \DOMText(($input) ? 'true' : 'false'));

            // integer
            elseif(is_int($input)) $element->appendChild(new \DOMText($input));

            // floats
            elseif(is_double($input)) $element->appendChild(new \DOMText($input));
            elseif(is_float($input)) $element->appendChild(new \DOMText($input));

            // a string?
            elseif (is_string($input)) {
                // characters that require a cdata wrapper
                $illegalCharacters = array('&', '<', '>', '"', '\'');

                // default we dont wrap with cdata tags
                $wrapCdata = false;

                // find illegal characters in input string
                foreach ($illegalCharacters as $character) {
                    if (stripos($input, $character) !== false) {
                        // wrap input with cdata
                        $wrapCdata = true;

                        // no need to search further
                        break;
                    }
                }

                // check if value contains illegal chars, if so wrap in CDATA
                if($wrapCdata) $element->appendChild(new \DOMCdataSection($input));

                // just regular element
                else $element->appendChild(new \DOMText($input));
            }

            // fallback
            else {
                if (self::DEBUG) {
                    echo 'Unknown type';
                    var_dump($input);
                    exit();
                }

                $element->appendChild(new \DOMText($input));
            }
        }

        // the value is an array
        else {
            // init var
            $isNonNumeric = false;

            // loop all elements
            foreach ($input as $index => $value) {
                // non numeric string as key?
                if (!is_numeric($index)) {
                    // reset var
                    $isNonNumeric = true;

                    // stop searching
                    break;
                }
            }

            // is there are named keys they should be handles as elements
            if($isNonNumeric) array_walk($input, array(__CLASS__, 'arrayToXML'), $element);

            // numeric elements means this a list of items
            else {
                // handle the value as an element
                foreach ($input as $value) {
                    if(is_array($value)) array_walk($value, array(__CLASS__, 'arrayToXML'), $element);
                }
            }
        }
    }

    /**
     * Decode the response
     *
     * @param  SimpleXMLElement $item   The item to decode.
     * @param  array[optional]  $return Just a placeholder.
     * @param  int[optional]    $i      A internal counter.
     * @return mixed
     */
    private static function decodeResponse($item, $return = null, $i = 0)
    {
        $arrayKeys = array('barcode', 'orderLine', 'additionalInsurance', 'infoDistributed', 'infoPugo');
        $integerKeys = array('totalPrice');

        if ($item instanceof SimpleXMLElement) {
            foreach ($item as $key => $value) {
                $attributes = (array) $value->attributes();

                if (!empty($attributes) && isset($attributes['@attributes'])) {
                    $return[$key]['@attributes'] = $attributes['@attributes'];
                }

                // empty
                if(isset($value['nil']) && (string) $value['nil'] === 'true') $return[$key] = null;

                // empty
                elseif (isset($value[0]) && (string) $value == '') {
                    if (in_array($key, $arrayKeys)) {
                        $return[$key][] = self::decodeResponse($value);
                    } else $return[$key] = self::decodeResponse($value, null, 1);
                } else {
                    // arrays
                    if (in_array($key, $arrayKeys)) {
                        $return[$key][] = (string) $value;
                    }

                    // booleans
                    elseif((string) $value == 'true') $return[$key] = true;
                    elseif((string) $value == 'false') $return[$key] = false;

                    // integers
                    elseif(in_array($key, $integerKeys)) $return[$key] = (int) $value;

                    // fallback to string
                    else $return[$key] = (string) $value;
                }
            }
        } else throw new Exception('Invalid item.');

        return $return;
    }

    /**
     * Make the call
     *
     * @param  string           $url       The URL to call.
     * @param  array[optional]  $data      The data to pass.
     * @param  array[optional]  $headers   The headers to pass.
     * @param  string[optional] $method    The HTTP-method to use.
     * @param  bool[optional]   $expectXML Do we expect XML?
     * @return mixed
     */
    private function doCall($url, $data = null, $headers = array(), $method = 'GET', $expectXML = true)
    {
        // any data?
        if ($data !== null) {
            // init XML
            $xml = new \DOMDocument('1.0', 'utf-8');

            // set some properties
            $xml->preserveWhiteSpace = false;
            $xml->formatOutput = true;

            // build data
            array_walk($data, array(__CLASS__, 'arrayToXML'), $xml);

            // store body
            $body = $xml->saveXML();
        } else $body = null;

        // build Authorization header
        $headers[] = 'Authorization: Basic ' . $this->getAuthorizationHeader();

        // set options
        $options[CURLOPT_URL] = self::API_URL . '/' . $this->accountId . $url;
        if($this->getPort() != 0) $options[CURLOPT_PORT] = $this->getPort();
        $options[CURLOPT_USERAGENT] = $this->getUserAgent();
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_TIMEOUT] = (int) $this->getTimeOut();
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        $options[CURLOPT_HTTPHEADER] = $headers;

        // PUT
        if ($method == 'PUT') {
            $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
            if($body != null) $options[CURLOPT_POSTFIELDS] = $body;
        }
        if ($method == 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $body;
        }

        // init
        $this->curl = curl_init();

        // set options
        curl_setopt_array($this->curl, $options);

        // execute
        $response = curl_exec($this->curl);
        $headers = curl_getinfo($this->curl);

        // fetch errors
        $errorNumber = curl_errno($this->curl);
        $errorMessage = curl_error($this->curl);

        // error?
        if ($errorNumber != '') {
            // internal debugging enabled
            if (self::DEBUG) {
                echo '<pre>';
                var_dump(htmlentities($response));
                var_dump($this);
                echo '</pre>';
            }

            throw new Exception($errorMessage, $errorNumber);
        }

        // valid HTTP-code
        if (!in_array($headers['http_code'], array(0, 200))) {
            // internal debugging enabled
            if (self::DEBUG) {
                echo '<pre>';
                var_dump($options);
                var_dump(htmlentities($body));
                var_dump($response);
                var_dump($headers);
                var_dump($this);
                echo '</pre>';
            }

            if ($expectXML) {
                // convert into XML
                $xml = simplexml_load_string($response);

                // validate
                if ($xml->getName() == 'businessException') {
                    // internal debugging enabled
                    if (self::DEBUG) {
                        echo '<pre>';
                        var_dump($response);
                        var_dump($headers);
                        var_dump($this);
                        echo '</pre>';
                    }

                    // message
                    $message = (string) $xml->message;
                    $code = (string) $xml->code;

                    // throw exception
                    throw new Exception($message, $code);
                }
            }

            throw new Exception('Invalid response.', $headers['http_code']);
        }

        // if we don't expect XML we can return the content here
        if(!$expectXML) return $response;

        // convert into XML
        $xml = simplexml_load_string($response);

        // validate
        if ($xml->getName() == 'businessException') {
            // internal debugging enabled
            if (self::DEBUG) {
                echo '<pre>';
                var_dump($response);
                var_dump($headers);
                var_dump($this);
                echo '</pre>';
            }

            // message
            $message = (string) $xml->message;
            $code = (string) $xml->code;

            // throw exception
            throw new Exception($message, $code);
        }

        // return the response
        return $xml;
    }

    /**
     * Get the account id
     *
     * @return string
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Generate the secret string for the Authorization header
     *
     * @return string
     */
    private function getAuthorizationHeader()
    {
        return base64_encode($this->accountId . ':' . $this->passPhrase);
    }

    /**
     * Get the passPhrase
     *
     * @return string
     */
    public function getPassPhrase()
    {
        return $this->passPhrase;
    }

    /**
     * Get the port
     *
     * @return int
     */
    public function getPort()
    {
        return (int) $this->port;
    }

    /**
     * Get the timeout that will be used
     *
     * @return int
     */
    public function getTimeOut()
    {
        return (int) $this->timeOut;
    }

    /**
     * Get the useragent that will be used.
     * Our version will be prepended to yours.
     * It will look like: "PHP bPost/<version> <your-user-agent>"
     *
     * @return string
     */
    public function getUserAgent()
    {
        return (string) 'PHP bPost/' . self::VERSION . ' ' . $this->userAgent;
    }

    /**
     * Set the timeout
     * After this time the request will stop. You should handle any errors triggered by this.
     *
     * @param int $seconds The timeout in seconds.
     */
    public function setTimeOut($seconds)
    {
        $this->timeOut = (int) $seconds;
    }

    /**
     * Set the user-agent for you application
     * It will be appended to ours, the result will look like: "PHP bPost/<version> <your-user-agent>"
     *
     * @param string $userAgent Your user-agent, it should look like <app-name>/<app-version>.
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = (string) $userAgent;
    }

    // webservice methods
// orders
    /**
     * Creates a new order. If an order with the same orderReference already exists
     *
     * @param  bPostOrder $order
     * @return bool
     */
    public function createOrReplaceOrder(Order $order)
    {
        // build url
        $url = '/orders';

        // build data
        $data['order']['@attributes']['xmlns'] = 'http://schema.post.be/shm/deepintegration/v2/';
        $data['order']['value'] = $order->toXMLArray($this->accountId);

        // build headers
        $headers = array(
            'Content-type: application/vnd.bpost.shm-order-v2+XML'
        );

        // make the call
        return ($this->doCall($url, $data, $headers, 'POST', false) == '');
    }

    /**
     * Fetch an order
     *
     * @param  string $reference
     * @return array
     */
    public function fetchOrder($reference)
    {
        // build url
        $url = '/orders/' . (string) $reference;

        // make the call
        $return = self::decodeResponse($this->doCall($url));

        // for some reason the order-data is wrapped in an order tag sometimes.
        if (isset($return['order'])) {
            if(isset($return['barcode'])) $barcodes = $return['barcode'];
            $return = $return['order'];
        }

        $order = new Order($return['orderReference']);

        if(isset($barcodes)) $order->setBarcodes($barcodes);

        if(isset($return['status'])) $order->setStatus($return['status']);
        if(isset($return['costCenter'])) $order->setCostCenter($return['costCenter']);

        // order lines
        if (isset($return['orderLine']) && !empty($return['orderLine'])) {
            foreach ($return['orderLine'] as $row) {
                $order->addOrderLine($row['text'], $row['nbOfItems']);
            }
        }

        // customer
        if (isset($return['customer'])) {
            // create customer
            $customer = new Customer($return['customer']['firstName'], $return['customer']['lastName']);
            if (isset($return['customer']['deliveryAddress'])) {
                $address = new Address(
                    $return['customer']['deliveryAddress']['streetName'],
                    $return['customer']['deliveryAddress']['number'],
                    $return['customer']['deliveryAddress']['postalCode'],
                    $return['customer']['deliveryAddress']['locality'],
                    $return['customer']['deliveryAddress']['countryCode']
                );
                if (isset($return['customer']['deliveryAddress']['box'])) {
                    $address->setBox($return['customer']['deliveryAddress']['box']);
                }
                $customer->setDeliveryAddress($address);
            }
            if(isset($return['customer']['email'])) $customer->setEmail($return['customer']['email']);
            if(isset($return['customer']['phoneNumber'])) $customer->setPhoneNumber($return['customer']['phoneNumber']);

            $order->setCustomer($customer);
        }

        // delivery method
        if (isset($return['deliveryMethod'])) {
            // atHome?
            if (isset($return['deliveryMethod']['atHome'])) {
                $deliveryMethod = new DeliveryMethodAtHome();

                // options
                if (isset($return['deliveryMethod']['atHome']['normal']['options']) && !empty($return['deliveryMethod']['atHome']['normal']['options'])) {
                    $options = array();

                    foreach ($return['deliveryMethod']['atHome']['normal']['options'] as $key => $row) {
                        $language = 'NL';	// @todo fix me
                        $emailAddress = null;
                        $mobilePhone = null;
                        $fixedPhone = null;

                        if(isset($row['emailAddress'])) $emailAddress = $row['emailAddress'];
                        if(isset($row['mobilePhone'])) $mobilePhone = $row['mobilePhone'];
                        if(isset($row['fixedPhone'])) $fixedPhone = $row['fixedPhone'];

                        if($emailAddress === null && $mobilePhone === null && $fixedPhone === null) continue;

                        $options[$key] = new Notification($language, $emailAddress, $mobilePhone, $fixedPhone);
                    }

                    $deliveryMethod->setNormal($options);
                }

                $order->setDeliveryMethod($deliveryMethod);
            }

            // atShop
            elseif (isset($return['deliveryMethod']['atShop'])) {
                $deliveryMethod = new DeliveryMethodAtShop();

                $language = $return['deliveryMethod']['atShop']['infoPugo']['@attributes']['language'];
                $emailAddress = null;
                $mobilePhone = null;
                $fixedPhone = null;

                if (isset($return['deliveryMethod']['atShop']['infoPugo'][0]['emailAddress'])) {
                    $emailAddress = $return['deliveryMethod']['atShop']['infoPugo'][0]['emailAddress'];
                }
                if (isset($return['deliveryMethod']['atShop']['infoPugo'][0]['mobilePhone'])) {
                    $mobilePhone = $return['deliveryMethod']['atShop']['infoPugo'][0]['mobilePhone'];
                }
                if (isset($return['deliveryMethod']['atShop']['infoPugo'][0]['fixedPhone'])) {
                    $fixedPhone = $return['deliveryMethod']['atShop']['infoPugo'][0]['fixedPhone'];
                }

                $deliveryMethod->setInfoPugo(
                    $return['deliveryMethod']['atShop']['infoPugo'][0]['pugoId'],
                    $return['deliveryMethod']['atShop']['infoPugo'][0]['pugoName'],
                    new Notification($language, $emailAddress, $mobilePhone, $fixedPhone)
                );

                if (isset($return['deliveryMethod']['atShop']['insurance']['additionalInsurance']['@attributes']['value'])) {
                    $deliveryMethod->setInsurance((int) $return['deliveryMethod']['atShop']['insurance']['additionalInsurance']['@attributes']['value']);
                }

                $language = $return['deliveryMethod']['atShop']['infoPugo']['@attributes']['language'];
                $emailAddress = null;
                $mobilePhone = null;
                $fixedPhone = null;
                $pugoId = null;
                $pugoName = null;

                if (isset($return['deliveryMethod']['atShop']['infoPugo'][0]['emailAddress'])) {
                    $emailAddress = $return['deliveryMethod']['atShop']['infoPugo'][0]['emailAddress'];
                }
                if (isset($return['deliveryMethod']['atShop']['infoPugo'][0]['mobilePhone'])) {
                    $mobilePhone = $return['deliveryMethod']['atShop']['infoPugo'][0]['mobilePhone'];
                }
                if (isset($return['deliveryMethod']['atShop']['infoPugo'][0]['fixedPhone'])) {
                    $fixedPhone = $return['deliveryMethod']['atShop']['infoPugo'][0]['fixedPhone'];
                }
                if (isset($return['deliveryMethod']['atShop']['infoPugo'][0]['pugoId'])) {
                    $pugoId = $return['deliveryMethod']['atShop']['infoPugo'][0]['pugoId'];
                }
                if (isset($return['deliveryMethod']['atShop']['infoPugo'][0]['pugoName'])) {
                    $pugoName = $return['deliveryMethod']['atShop']['infoPugo'][0]['pugoName'];
                }

                $deliveryMethod->setInfoPugo(
                    $pugoId, $pugoName,
                    new Notification($language, $emailAddress, $mobilePhone, $fixedPhone)
                );

                $order->setDeliveryMethod($deliveryMethod);
            }

            // at24-7
            elseif (isset($return['deliveryMethod']['at24-7'])) {
                $deliveryMethod = new DeliveryMethodAt247(
                    $return['deliveryMethod']['at24-7']['infoParcelsDepot']['parcelsDepotId']
                );
                if (isset($return['deliveryMethod']['at24-7']['memberId'])) {
                    $deliveryMethod->setMemberId($return['deliveryMethod']['at24-7']['memberId']);
                }
                if (isset($return['deliveryMethod']['at24-7']['signature']['signature'])) {
                    $deliveryMethod->setSignature();
                }
                if (isset($return['deliveryMethod']['at24-7']['signature']['signature'])) {
                    $deliveryMethod->setSignature(true);
                }
                if (isset($return['deliveryMethod']['at24-7']['insurance']['additionalInsurance']['@attributes']['value'])) {
                    $deliveryMethod->setInsurance((int) $return['deliveryMethod']['at24-7']['insurance']['additionalInsurance']['@attributes']['value']);
                }

                $order->setDeliveryMethod($deliveryMethod);
            }

            // intExpress?
            elseif (isset($return['deliveryMethod']['intExpress'])) {
                $deliveryMethod = new DeliveryMethodIntBusiness();

                if (isset($return['deliveryMethod']['intExpress']['insured']['additionalInsurance']['@attributes']['value'])) {
                    $deliveryMethod->setInsurance((int) $return['deliveryMethod']['intExpress']['insured']['additionalInsurance']['@attributes']['value']);
                }

                // options
                if (isset($return['deliveryMethod']['intBusiness']['insured']['options']) && !empty($return['deliveryMethod']['intExpress']['insured']['options'])) {
                    $options = array();

                    foreach ($return['deliveryMethod']['intExpress']['insured']['options'] as $key => $row) {
                        $language = 'NL';	// @todo fix me
                        $emailAddress = null;
                        $mobilePhone = null;
                        $fixedPhone = null;

                        if(isset($row['emailAddress'])) $emailAddress = $row['emailAddress'];
                        if(isset($row['mobilePhone'])) $mobilePhone = $row['mobilePhone'];
                        if(isset($row['fixedPhone'])) $fixedPhone = $row['fixedPhone'];

                        if($emailAddress === null && $mobilePhone === null && $fixedPhone === null) continue;

                        $options[$key] = new Notification($language, $emailAddress, $mobilePhone, $fixedPhone);
                    }

                    $deliveryMethod->setInsured($options);
                }

                $order->setDeliveryMethod($deliveryMethod);
            }

            // intBusiness?
            elseif (isset($return['deliveryMethod']['intBusiness'])) {
                $deliveryMethod = new DeliveryMethodIntBusiness();

                if (isset($return['deliveryMethod']['intBusiness']['insured']['additionalInsurance']['@attributes']['value'])) {
                    $deliveryMethod->setInsurance((int) $return['deliveryMethod']['intBusiness']['insured']['additionalInsurance']['@attributes']['value']);
                }

                // options
                if (isset($return['deliveryMethod']['intBusiness']['insured']['options']) && !empty($return['deliveryMethod']['intBusiness']['insured']['options'])) {
                    $options = array();

                    foreach ($return['deliveryMethod']['intBusiness']['insured']['options'] as $key => $row) {
                        $language = 'NL';	// @todo fix me
                        $emailAddress = null;
                        $mobilePhone = null;
                        $fixedPhone = null;

                        if(isset($row['emailAddress'])) $emailAddress = $row['emailAddress'];
                        if(isset($row['mobilePhone'])) $mobilePhone = $row['mobilePhone'];
                        if(isset($row['fixedPhone'])) $fixedPhone = $row['fixedPhone'];

                        if($emailAddress === null && $mobilePhone === null && $fixedPhone === null) continue;

                        $options[$key] = new Notification($language, $emailAddress, $mobilePhone, $fixedPhone);
                    }

                    $deliveryMethod->setInsured($options);
                }

                $order->setDeliveryMethod($deliveryMethod);
            }
        }

        // total price
        if(isset($return['totalPrice'])) $order->setTotal($return['totalPrice']);

        return $order;
    }

    /**
     * Modify the status for an order.
     *
     * @param  string $reference The reference for an order
     * @param  string $status    The new status, allowed values are: OPEN, PENDING, CANCELLED, COMPLETED or ON-HOLD
     * @return bool
     */
    public function modifyOrderStatus($reference, $status)
    {
        $allowedStatuses = array('OPEN', 'PENDING', 'CANCELLED', 'COMPLETED', 'ON-HOLD');
        $status = mb_strtoupper((string) $status);

        // validate
        if (!in_array($status, $allowedStatuses)) {
            throw new Exception(
                'Invalid status (' . $status . '), allowed values are: ' .
                implode(', ', $allowedStatuses) . '.'
            );
        }

        // build url
        $url = '/orders/status';

        // build data
        $data['orderStatusMap']['@attributes']['xmlns'] = 'http://schema.post.be/shm/deepintegration/v2/';
        $data['orderStatusMap']['entry']['orderReference'] = (string) $reference;
        $data['orderStatusMap']['entry']['status'] = $status;

        // build headers
        $headers = array(
            'X-HTTP-Method-Override: PATCH',
            'Content-type: application/vnd.bpost.shm-order-status-v2+XML'
        );

        // make the call
        return ($this->doCall($url, $data, $headers, 'PUT', false) == '');
    }

// labels
    /**
     * Create a national label
     *
     * @param  string           $reference    Order reference: unique ID used in your web shop to assign to an order.
     * @param  int              $amount       Amount of labels.
     * @param  bool[optional]   $withRetour   Should the return labeks be included?
     * @param  bool[optional]   $returnLabels Should the labels be included?
     * @param  string[optional] $labelFormat  Format of the labels, possible values are: A_4, A_5.
     * @return array
     */
    public function createNationalLabel($reference, $amount, $withRetour = null, $returnLabels = null, $labelFormat = null)
    {
        $allowedLabelFormats = array('A_4', 'A_5');

        // validate
        if ($labelFormat !== null && !in_array($labelFormat, $allowedLabelFormats)) {
            throw new Exception(
                'Invalid value for labelFormat (' . $labelFormat . '), allowed values are: ' .
                implode(', ', $allowedLabelFormats) . '.'
            );
        }

        // build url
        $url = '/labels';

        if($labelFormat !== null) $url .= '?labelFormat=' . $labelFormat;

        // build data
        $data['orderRefLabelAmountMap']['@attributes']['xmlns'] = 'http://schema.post.be/shm/deepintegration/v2/';
        $data['orderRefLabelAmountMap']['entry']['orderReference'] = (string) $reference;
        $data['orderRefLabelAmountMap']['entry']['labelAmount'] = (int) $amount;
        if($withRetour !== null) $data['orderRefLabelAmountMap']['entry']['withRetour'] = (bool) $withRetour;
        if($returnLabels !== null) $data['orderRefLabelAmountMap']['entry']['returnLabels'] = ($returnLabels) ? '1' : '0';

        // build headers
        $headers = array(
            'Content-type: application/vnd.bpost.shm-nat-label-v2+XML'
        );

        // make the call
        $return = self::decodeResponse($this->doCall($url, $data, $headers, 'POST'));

        // validate
        if(!isset($return['entry'])) throw new Exception('Invalid response.');

        // return
        return $return['entry'];
    }

    /**
     * Create an international label
     *
     * @param  string         $reference    Order reference: unique ID used in your web shop to assign to an order.
     * @param  array          $labelInfo    For each label an object should be present
     * @param  bool[optional] $returnLabels Should the labels be included?
     * @return array
     */
    public function createInternationalLabel($reference, array $labelInfo, $returnLabels = null)
    {
        // build url
        $url = '/labels';

        // build data
        $data['internationalLabelInfos']['@attributes']['xmlns'] = 'http://schema.post.be/shm/deepintegration/v2/';

        foreach ($labelInfo as $row) {
            if (!($row instanceof InternationalLabelInfo)) {
                throw new Exception(
                    'Invalid value for labelInfo, should be an instance of InternationalLabelInfo'
                );
            }

            $data['internationalLabelInfos']['internationalLabelInfo'][] = $row->toXMLArray();
        }

        $data['internationalLabelInfos']['orderReference'] = (string) $reference;
        if($returnLabels !== null) $data['internationalLabelInfos']['returnLabels'] = (bool) $returnLabels;

        // build headers
        $headers = array(
            'Content-type: application/vnd.bpost.shm-int-label-v2+XML'
        );

        // make the call
        $return = self::decodeResponse($this->doCall($url, $data, $headers, 'POST'));

        // validate
        if(!isset($return['entry'])) throw new Exception('Invalid response.');

        // return
        return $return['entry'];
    }

    /**
     * Create an order and the labels
     *
     * @param  Order $order
     * @param  int        $amount
     * @return array
     */
    public function createOrderAndNationalLabel(Order $order, $amount)
    {
        // build url
        $url = '/orderAndLabels';

        // build data
        $data['orderWithLabelAmount']['@attributes']['xmlns'] = 'http://schema.post.be/shm/deepintegration/v2/';
        $data['orderWithLabelAmount']['order'] = $order->toXMLArray($this->accountId);
        $data['orderWithLabelAmount']['labelAmount'] = (int) $amount;

        // build headers
        $headers = array(
            'Content-type: application/vnd.bpost.shm-orderAndNatLabels-v2+XML'
        );

        // make the call
        $return = self::decodeResponse($this->doCall($url, $data, $headers, 'POST'));

        // validate
        if(!isset($return['entry'])) throw new Exception('Invalid response.');

        // return
        return $return['entry'];
    }

    /**
     * Create an order and an international label
     *
     * @param  array      $labelInfo The label info
     * @param  Order $order     The order
     * @return array
     */
    public function createOrderAndInternationalLabel(array $labelInfo, Order $order)
    {
        // build url
        $url = '/orderAndLabels';

        // build data
        $data['orderInternationalLabelInfos']['@attributes']['xmlns'] = 'http://schema.post.be/shm/deepintegration/v2/';
        foreach ($labelInfo as $row) {
            if (!($row instanceof InternationalLabelInfo)) {
                throw new Exception(
                    'Invalid value for labelInfo, should be an instance of InternationalLabelInfo'
                );
            }

            $data['orderInternationalLabelInfos']['internationalLabelInfo'][] = $row->toXMLArray();
        }
        $data['orderInternationalLabelInfos']['order'] = $order->toXMLArray($this->accountId);

        // build headers
        $headers = array(
            'Content-type: application/vnd.bpost.shm-orderAndIntLabels-v2+XML'
        );

        // make the call
        $return = self::decodeResponse($this->doCall($url, $data, $headers, 'POST'));

        // validate
        if(!isset($return['entry'])) throw new Exception('Invalid response.');

        // return
        return $return['entry'];
    }

    /**
     * Retrieve a PDF-label for a box
     *
     * @param  string           $barcode     The barcode to retrieve
     * @param  string[optional] $labelFormat Possible values are: A_4, A_5
     * @return string
     */
    public function retrievePDFLabelsForBox($barcode, $labelFormat = null)
    {
        $allowedLabelFormats = array('A_4', 'A_5');

        // validate
        if ($labelFormat !== null && !in_array($labelFormat, $allowedLabelFormats)) {
            throw new Exception(
                'Invalid value for labelFormat (' . $labelFormat . '), allowed values are: ' .
                implode(', ', $allowedLabelFormats) . '.'
            );
        }

        // build url
        $url = '/labels/' . (string) $barcode . '/pdf';

        if($labelFormat !== null) $url .= '?labelFormat=' . $labelFormat;

        // build headers
        $headers = array(
            'Accept: application/vnd.bpost.shm-pdf-v2+XML'
        );

        // make the call
        return (string) $this->doCall($url, null, $headers);
    }

    /**
     * Retrieve a PDF-label for an order
     *
     * @param  string           $reference
     * @param  string[optional] $labelFormat Possible values are: A_4, A_5
     * @return string
     */
    public function retrievePDFLabelsForOrder($reference, $labelFormat = null)
    {
        $allowedLabelFormats = array('A_4', 'A_5');

        // validate
        if ($labelFormat !== null && !in_array($labelFormat, $allowedLabelFormats)) {
            throw new Exception(
                'Invalid value for labelFormat (' . $labelFormat . '), allowed values are: ' .
                implode(', ', $allowedLabelFormats) . '.'
            );
        }

        // build url
        $url = '/orders/' . (string) $reference . '/pdf';

        if($labelFormat !== null) $url .= '?labelFormat=' . $labelFormat;

        // build headers
        $headers = array(
            'Accept: application/vnd.bpost.shm-pdf-v2+XML'
        );

        // make the call
        return (string) $this->doCall($url, null, $headers);
    }
}





