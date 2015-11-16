<?php
namespace TijsVerkoyen\Bpost;

use TijsVerkoyen\Bpost\Bpack247\Customer;

/**
 * bPost Bpack24/7 class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class Bpack247
{
    // URL for the api
    const API_URL = 'http://www.bpack247.be/BpostRegistrationWebserviceREST/servicecontroller.svc';

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
    private $timeOut = 30;

    /**
     * The user agent
     *
     * @var string
     */
    private $userAgent;

    /**
     * Make the call
     *
     * @param  string $url    The URL to call.
     * @param  string $body   The data to pass.
     * @param  string $method The HTTP-method to use.
     * @return mixed
     */
    private function doCall($url, $body = null, $method = 'GET')
    {
        // build Authorization header
        $headers[] = 'Authorization: Basic ' . $this->getAuthorizationHeader();

        // set options
        $options[CURLOPT_URL] = self::API_URL . $url;
        $options[CURLOPT_USERAGENT] = $this->getUserAgent();
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_TIMEOUT] = (int) $this->getTimeOut();
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        $options[CURLOPT_HTTPHEADER] = $headers;

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
            throw new Exception($errorMessage, $errorNumber);
        }

        // valid HTTP-code
        if (!in_array($headers['http_code'], array(0, 200))) {
            $xml = @simplexml_load_string($response);

            if ($xml !== false && ($xml->getName() == 'businessException' || $xml->getName() == 'validationException')
            ) {
                $message = (string) $xml->message;
                $code = isset($xml->code) ? (int) $xml->code : null;
                throw new Exception($message, $code);
            }

            throw new Exception('Invalid response.', $headers['http_code']);
        }

        // convert into XML
        $xml = simplexml_load_string($response);

        // validate
        if ($xml->getName() == 'businessException') {
            $message = (string) $xml->message;
            $code = (string) $xml->code;
            throw new Exception($message, $code);
        }

        // return the response
        return $xml;
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
        return (string) 'PHP Bpost Bpack247/' . self::VERSION . ' ' . $this->userAgent;
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

    // webservice methods
    public function createMember(Customer $customer)
    {
        $url = '/customer';

        $document = new \DOMDocument('1.0', 'utf-8');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;

        $document->appendChild(
            $customer->toXML(
                $document
            )
        );

        return $this->doCall(
            $url,
            $document->saveXML(),
            'POST'
        );
    }

    /**
     * Retrieve member information
     *
     * @param  string   $id
     * @return Customer
     */
    public function getMember($id)
    {
        $xml = $this->doCall(
            '/customer/' . $id
        );

        return Customer::createFromXML($xml);
    }
}
