<?php
namespace TijsVerkoyen\Bpost;

use TijsVerkoyen\Bpost\Bpost\Order;
use TijsVerkoyen\Bpost\Bpost\Order\Box;

/**
 * Bpost class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class Bpost
{
    // internal constant to enable/disable debugging
    const DEBUG = false;

    // URL for the api
    const API_URL = 'https://api.bpost.be/services/shm';

    // current version
    const VERSION = '3.0.0';

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
     * Create Bpost instance
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
        if ($this->curl !== null) {
            curl_close($this->curl);
            $this->curl = null;
        }
    }

    /**
     * Decode the response
     *
     * @param  SimpleXMLElement $item   The item to decode.
     * @param  array            $return Just a placeholder.
     * @param  int              $i      A internal counter.
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
                if (isset($value['nil']) && (string) $value['nil'] === 'true') {
                    $return[$key] = null;
                } // empty
                elseif (isset($value[0]) && (string) $value == '') {
                    if (in_array($key, $arrayKeys)) {
                        $return[$key][] = self::decodeResponse($value);
                    } else {
                        $return[$key] = self::decodeResponse($value, null, 1);
                    }
                } else {
                    // arrays
                    if (in_array($key, $arrayKeys)) {
                        $return[$key][] = (string) $value;
                    } // booleans
                    elseif ((string) $value == 'true') {
                        $return[$key] = true;
                    } elseif ((string) $value == 'false') {
                        $return[$key] = false;
                    } // integers
                    elseif (in_array($key, $integerKeys)) {
                        $return[$key] = (int) $value;
                    } // fallback to string
                    else {
                        $return[$key] = (string) $value;
                    }
                }
            }
        } else {
            throw new Exception('Invalid item.');
        }

        return $return;
    }

    /**
     * Make the call
     *
     * @param  string $url       The URL to call.
     * @param  string $body      The data to pass.
     * @param  array  $headers   The headers to pass.
     * @param  string $method    The HTTP-method to use.
     * @param  bool   $expectXML Do we expect XML?
     * @return mixed
     */
    private function doCall($url, $body = null, $headers = array(), $method = 'GET', $expectXML = true)
    {
        // build Authorization header
        $headers[] = 'Authorization: Basic ' . $this->getAuthorizationHeader();

        // set options
        $options[CURLOPT_URL] = self::API_URL . '/' . $this->accountId . $url;
        if ($this->getPort() != 0) {
            $options[CURLOPT_PORT] = $this->getPort();
        }
        $options[CURLOPT_USERAGENT] = $this->getUserAgent();
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_TIMEOUT] = (int) $this->getTimeOut();
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        $options[CURLOPT_HTTPHEADER] = $headers;

        // PUT
        if ($method == 'PUT') {
            $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
            if ($body != null) {
                $options[CURLOPT_POSTFIELDS] = $body;
            }
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
        if (!in_array($headers['http_code'], array(0, 200, 201))) {
            // internal debugging enabled
            if (self::DEBUG) {
                echo '<pre>';
                var_dump($options);
                var_dump($response);
                var_dump($headers);
                var_dump($this);
                echo '</pre>';
            }

            // convert into XML
            $xml = @simplexml_load_string($response);

            // validate
            if ($xml !== false && (substr($xml->getName(), 0, 7) == 'invalid')
            ) {
                // internal debugging enabled
                if (self::DEBUG) {
                    echo '<pre>';
                    var_dump($response);
                    var_dump($headers);
                    var_dump($this);
                    echo '</pre>';
                }

                // message
                $message = (string) $xml->error;
                $code = isset($xml->code) ? (int) $xml->code : null;

                // throw exception
                throw new Exception($message, $code);
            }

            if (isset($headers['content_type']) && substr_count($headers['content_type'], 'text/plain') > 0) {
                $message = $response;
            } else {
                $message = 'Invalid response.';
            }

            throw new Exception($message, $headers['http_code']);
        }

        // if we don't expect XML we can return the content here
        if (!$expectXML) {
            return $response;
        }

        // convert into XML
        $xml = simplexml_load_string($response);

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
     * It will look like: "PHP Bpost/<version> <your-user-agent>"
     *
     * @return string
     */
    public function getUserAgent()
    {
        return (string) 'PHP Bpost/' . self::VERSION . ' ' . $this->userAgent;
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
     * It will be appended to ours, the result will look like: "PHP Bpost/<version> <your-user-agent>"
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
     * @param  Order $order
     * @return bool
     */
    public function createOrReplaceOrder(Order $order)
    {
        $url = '/orders';

        $document = new \DOMDocument('1.0', 'utf-8');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;

        $document->appendChild(
            $order->toXML(
                $document,
                ACCOUNT_ID
            )
        );

        $headers = array(
            'Content-type: application/vnd.bpost.shm-order-v3+XML'
        );

        return (
            $this->doCall(
                $url,
                $document->saveXML(),
                $headers,
                'POST',
                false
            ) == ''
        );
    }

    /**
     * Fetch an order
     *
     * @param $reference
     * @return Order
     */
    public function fetchOrder($reference)
    {
        $url = '/orders/' . (string) $reference;

        $headers = array(
            'Accept: application/vnd.bpost.shm-order-v3+XML',
        );
        $xml = $this->doCall(
            $url,
            null,
            $headers
        );

        return Order::createFromXML($xml);
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
        $status = strtoupper($status);
        if (!in_array($status, Box::getPossibleStatusValues())) {
            throw new Exception(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', Box::getPossibleStatusValues())
                )
            );
        }

        $url = '/orders/' . $reference;

        $document = new \DOMDocument('1.0', 'utf-8');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;

        $orderUpdate = $document->createElement('orderUpdate');
        $orderUpdate->setAttribute('xmlns', 'http://schema.post.be/shm/deepintegration/v3/');
        $orderUpdate->setAttribute('xmlns:xsi', '"http://www.w3.org/2001/XMLSchema-instance');
        $orderUpdate->appendChild(
            $document->createElement('status', $status)
        );
        $document->appendChild($orderUpdate);

        $headers = array(
            'Content-type: application/vnd.bpost.shm-orderUpdate-v3+XML'
        );

        return (
            $this->doCall(
                $url,
                $document->saveXML(),
                $headers,
                'POST',
                false
            ) == ''
        );
    }

// labels
    /**
     * Create a national label
     *
     * @param  string $reference    Order reference: unique ID used in your web shop to assign to an order.
     * @param  int    $amount       Amount of labels.
     * @param  bool   $withRetour   Should the return labeks be included?
     * @param  bool   $returnLabels Should the labels be included?
     * @param  string $labelFormat  Format of the labels, possible values are: A_4, A_5.
     * @return array
     */
    public function createNationalLabel(
        $reference,
        $amount,
        $withRetour = null,
        $returnLabels = null,
        $labelFormat = null
    ) {
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

        if ($labelFormat !== null) {
            $url .= '?labelFormat=' . $labelFormat;
        }

        // build data
        $data['orderRefLabelAmountMap']['@attributes']['xmlns'] = 'http://schema.post.be/shm/deepintegration/v2/';
        $data['orderRefLabelAmountMap']['entry']['orderReference'] = (string) $reference;
        $data['orderRefLabelAmountMap']['entry']['labelAmount'] = (int) $amount;
        if ($withRetour !== null) {
            $data['orderRefLabelAmountMap']['entry']['withRetour'] = (bool) $withRetour;
        }
        if ($returnLabels !== null) {
            $data['orderRefLabelAmountMap']['entry']['returnLabels'] = ($returnLabels) ? '1' : '0';
        }

        // build headers
        $headers = array(
            'Content-type: application/vnd.bpost.shm-nat-label-v2+XML'
        );

        // make the call
        $return = self::decodeResponse($this->doCall($url, $data, $headers, 'POST'));

        // validate
        if (!isset($return['entry'])) {
            throw new Exception('Invalid response.');
        }

        // return
        return $return['entry'];
    }

    /**
     * Create an international label
     *
     * @param  string $reference    Order reference: unique ID used in your web shop to assign to an order.
     * @param  array  $labelInfo    For each label an object should be present
     * @param  bool   $returnLabels Should the labels be included?
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
        if ($returnLabels !== null) {
            $data['internationalLabelInfos']['returnLabels'] = (bool) $returnLabels;
        }

        // build headers
        $headers = array(
            'Content-type: application/vnd.bpost.shm-int-label-v2+XML'
        );

        // make the call
        $return = self::decodeResponse($this->doCall($url, $data, $headers, 'POST'));

        // validate
        if (!isset($return['entry'])) {
            throw new Exception('Invalid response.');
        }

        // return
        return $return['entry'];
    }

    /**
     * Create an order and the labels
     *
     * @param  Order $order
     * @param  int   $amount
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
        if (!isset($return['entry'])) {
            throw new Exception('Invalid response.');
        }

        // return
        return $return['entry'];
    }

    /**
     * Create an order and an international label
     *
     * @param  array $labelInfo The label info
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
        if (!isset($return['entry'])) {
            throw new Exception('Invalid response.');
        }

        // return
        return $return['entry'];
    }

    /**
     * Retrieve a PDF-label for a box
     *
     * @param  string $barcode     The barcode to retrieve
     * @param  string $labelFormat Possible values are: A_4, A_5
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

        if ($labelFormat !== null) {
            $url .= '?labelFormat=' . $labelFormat;
        }

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
     * @param  string $reference
     * @param  string $labelFormat Possible values are: A_4, A_5
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

        if ($labelFormat !== null) {
            $url .= '?labelFormat=' . $labelFormat;
        }

        // build headers
        $headers = array(
            'Accept: application/vnd.bpost.shm-pdf-v2+XML'
        );

        // make the call
        return (string) $this->doCall($url, null, $headers);
    }
}
