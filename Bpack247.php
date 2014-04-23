<?php
namespace TijsVerkoyen\Bpost;

use TijsVerkoyen\Bpost\Bpack247\Customer;
use TijsVerkoyen\Bpost\Exception;

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
    // internal constant to enable/disable debugging
    const DEBUG = true;

    // URL for the api
    const API_URL = 'http://www.bpack247.be/BpostRegistrationWebserviceREST/servicecontroller.svc';

    // current version
    const VERSION = '3';

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

    /**
     * Callback-method for elements in the return-array
     *
     * @param mixed       $input The value.
     * @param string      $key   string    The key.
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
                    if (is_bool($value)) {
                        $value = ($value) ? 'true' : 'false';
                    }

                    $node = new \DOMElement($name, $value);
                    $element->appendChild($node);
                }
            }

            return;
        }

        // skip attributes
        if ($key == '@attributes') {
            return;
        }

        // create element
        $element = new \DOMElement($key);

        // append
        $xml->appendChild($element);

        // no value? just stop here
        if ($input === null) {
            return;
        }

        // is it an array and are there attributes
        if (is_array($input) && isset($input['@attributes'])) {
            // loop attributes
            foreach ((array) $input['@attributes'] as $name => $value) {
                $element->setAttribute($name, $value);
            }

            // reset value
            if (count($input) == 2 && isset($input['value'])) {
                $input = $input['value'];
            } // reset the input if it is a single value
            elseif (count($input) == 1) {
                return;
            }
        }

        // the input isn't an array
        if (!is_array($input)) {
            // boolean
            if (is_bool($input)) {
                $element->appendChild(new \DOMText(($input) ? 'true' : 'false'));
            } // integer
            elseif (is_int($input)) {
                $element->appendChild(new \DOMText($input));
            } // floats
            elseif (is_double($input)) {
                $element->appendChild(new \DOMText($input));
            } elseif (is_float($input)) {
                $element->appendChild(new \DOMText($input));
            } // a string?
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
                if ($wrapCdata) {
                    $element->appendChild(new \DOMCdataSection($input));
                } // just regular element
                else {
                    $element->appendChild(new \DOMText($input));
                }
            } // fallback
            else {
                if (self::DEBUG) {
                    echo 'Unknown type';
                    var_dump($input);
                    exit();
                }

                $element->appendChild(new \DOMText($input));
            }
        } // the value is an array
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
            if ($isNonNumeric) {
                array_walk($input, array(__CLASS__, 'arrayToXML'), $element);
            } // numeric elements means this a list of items
            else {
                // handle the value as an element
                foreach ($input as $value) {
                    if (is_array($value)) {
                        array_walk($value, array(__CLASS__, 'arrayToXML'), $element);
                    }
                }
            }
        }
    }

    /**
     * Make the call
     *
     * @param  string            $url       The URL to call.
     * @param  array  [optional] $data      The data to pass.
     * @param  string [optional] $method    The HTTP-method to use.
     * @return mixed
     */
    private function doCall($url, $data = null, $method = 'GET')
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
        } else {
            $body = null;
        }

        // set options
        $options[CURLOPT_URL] = self::API_URL . $url;
        $options[CURLOPT_USERAGENT] = $this->getUserAgent();
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_TIMEOUT] = (int) $this->getTimeOut();
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;

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

            // convert into XML
            $xml = @simplexml_load_string($response);

            // validate
            if ($xml !== false && ($xml->getName() == 'businessException' || $xml->getName() == 'validationException')
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
                $message = (string) $xml->message;
                $code = isset($xml->code) ? (int) $xml->code : null;

                // throw exception
                throw new Exception($message, $code);
            }

            throw new Exception('Invalid response.', $headers['http_code']);
        }

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

    // webservice methods
    public function createMember(Customer $customer)
    {
        // build url
        $url = '/customer';

        // build data
        $data['Customer']['@attributes'] = array(
            'xmlns' => 'http://schema.post.be/ServiceController/customer',
        );
        $data['Customer']['value'] = $customer->toXMLArray();

        // make the call
        return $this->doCall($url, $data, 'POST');


        $data = array(
            ''
        );

        $xml = $this->doCall('/customer', $customer->toXMLArray(), 'POST');

        var_dump($xml);
    }


    public function getMember($id)
    {
        $xml = $this->doCall('lightcustomer/' . $id);

        var_dump($xml);
        exit;

        if (!isset($xml->Poi->Record)) {
            throw new Exception('Invalid XML-response');
        }

        return Poi::createFromXML($xml->Poi->Record);

    }
}
