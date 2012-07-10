<?php

/**
 * bPost class
 *
 * This source file can be used to communicate with the bPost Shipping Manager API
 *
 * The class is documented in the file itself. If you find any bugs help me out and report them. Reporting can be done by sending an email to php-bpost-bugs[at]verkoyen[dot]eu.
 * If you report a bug, make sure you give me enough information (include your code).
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
 * @version 1.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license BSD License
 */
class bPost
{
	// internal constant to enable/disable debugging
	const DEBUG = true;

	// URL for the api
	const API_URL = 'https://api.bpost.be/services/shm';

	// current version
	const VERSION = '1.0.0';

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
	 * The passphrase
	 *
	 * @var string
	 */
	private $passphrase;

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
	 * Default constructor
	 */
	public function __construct($accountId, $passphrase)
	{
		$this->accountId = (string) $accountId;
		$this->passphrase = (string) $passphrase;
	}

	/**
	 * Destructor
	 */
	public function __destruct()
	{
		// is the connection open?
		if($this->curl !== null)
		{
			// close connection
			curl_close($this->curl);

			// reset
			$this->curl = null;
		}
	}

	/**
	 * Callback-method for elements in the return-array
	 *
	 * @param mixed $input			The value.
	 * @param string $key string	The key.
	 * @param array $data			Some data.
	 */
	private static function arrayToXML(&$input, $key, $data)
	{
		$XML = $data[0];
		$removeNullKeys = (bool) $data[1];
		$sort = (bool) $data[2];

		// skip attributes
		if($key == '@attributes') return;

		if($removeNullKeys && is_null($input)) return;

		// create element
		$element = new DOMElement($key);

		// append
		$XML->appendChild($element);

		// no value? just stop here
		if($input === null) return;

		// is it an array and are there attributes
		if(is_array($input) && isset($input['@attributes']))
		{
			// loop attributes
			foreach((array) $input['@attributes'] as $name => $value) $element->setAttribute($name, $value);

			// reset the input if it is a single value
			if(count($input) == 1)
			{
				// get keys
				$keys = array_keys($input);

				// reset
				$input = $input[$keys[0]];
			}
		}

		// the input isn't an array
		if(!is_array($input))
		{
			// boolean
			if(is_bool($input)) $element->appendChild(new DOMText(($input) ? 'true' : 'false'));

			// integer
			elseif(is_int($input)) $element->appendChild(new DOMText($input));

			// floats
			elseif(is_double($input)) $element->appendChild(new DOMText($input));
			elseif(is_float($input)) $element->appendChild(new DOMText($input));

			// a string?
			elseif(is_string($input))
			{
				// characters that require a cdata wrapper
				$illegalCharacters = array('&', '<', '>', '"', '\'');

				// default we dont wrap with cdata tags
				$wrapCdata = false;

				// find illegal characters in input string
				foreach($illegalCharacters as $character)
				{
					if(stripos($input, $character) !== false)
					{
						// wrap input with cdata
						$wrapCdata = true;

						// no need to search further
						break;
					}
				}

				// check if value contains illegal chars, if so wrap in CDATA
				if($wrapCdata) $element->appendChild(new DOMCdataSection($input));

				// just regular element
				else $element->appendChild(new DOMText($input));
			}

			// fallback
			else
			{
				if(self::DEBUG)
				{
					echo 'Unknown type';
					var_dump($input);
					exit();
				}

				$element->appendChild(new DOMText($input));
			}
		}

		// the value is an array
		else
		{
			if(!empty($input) && $sort) ksort($input);

			// init var
			$isNonNumeric = false;

			// loop all elements
			foreach($input as $index => $value)
			{
				// non numeric string as key?
				if(!is_numeric($index))
				{
					// reset var
					$isNonNumeric = true;

					// stop searching
					break;
				}
			}

			// is there are named keys they should be handles as elements
			if($isNonNumeric) array_walk($input, array('bPost', 'arrayToXML'), array($element, $removeNullKeys, $sort));

			// numeric elements means this a list of items
			else
			{
				// handle the value as an element
				foreach($input as $value)
				{
					if(is_array($value)) array_walk($value, array('bPost', 'arrayToXML'), array($element, $removeNullKeys, $sort));
				}
			}
		}
	}

	/**
	 * Decode the response
	 *
	 * @param SimpleXMLElement $item	The item to decode.
	 * @param array[optional] $return	Just a placeholder.
	 * @param int[optional] $i			A internal counter.
	 * @return mixed
	 */
	private function decodeResponse($item, $return = null, $i = 0)
	{
		$integerKeys = array('totalPrice');
		$arrayKeys = array();

		if($item instanceof SimpleXMLElement)
		{
			foreach($item as $key => $value)
			{
				// empty
				if(isset($value['nil']) && (string) $value['nil'] === 'true') $return[$key] = null;

				// empty
				elseif(isset($value[0]) && (string) $value == '')
				{
					if(in_array($key, $arrayKeys))
					{
						foreach($value as $row)
						{
							$return[$key][] = self::decodeResponse($row);
						}
					}

					else $return[$key] = self::decodeResponse($value, null, 1);
				}

				else
				{
					// arrays
					if(in_array($key, $arrayKeys))
					{
						foreach($value as $row)
						{
							$return[$key][] = self::decodeResponse($row);
						}
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
		}

		else throw new bPostException('invalid item');

		return $return;
	}

	/**
	 * Make the call
	 *
	 * @param string $method					The method to be called.
	 * @param array[optional] $data				The data to pass.
	 * @param bool[optional] $includeContext	Should we include the context, if available.
	 * @param bool[optional] $overruleKey		The method is wrapped, but for several methods this can't be done automatically.
	 * @param bool[optional] $removeNullValues	Should null values be removed from the XML?
	 * @param bool[optional] $sort				Should the passed data be sorted?
	 * @return mixed
	 */
	private function doCall($url, $data = null, $method = 'GET')
	{
//		// init XML
//		$XML = new DOMDocument('1.0', 'utf-8');
//
//		// set some properties
//		$XML->preserveWhiteSpace = false;
//		$XML->formatOutput = true;
//
//		// create root element
//		$root = $XML->createElement('soap:Envelope');
//		$root->setAttribute('xmlns:soap', 'http://schemas.xmlsoap.org/soap/envelope/');
//		$root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
//		$root->setAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
//		$root->setAttribute('xmlns', 'http://www.bpost.be/webshop/v1.3/');
//		$XML->appendChild($root);
//
//		// create body
//		$body = $XML->createElement('soap:Body');
//		$root->appendChild($body);

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
//		$options[CURLOPT_POST] = true;
//		$options[CURLOPT_POSTFIELDS] = $body;

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
		if($errorNumber != '')
		{
			// internal debugging enabled
			if(self::DEBUG)
			{
				echo '<pre>';
				var_dump(htmlentities($response));
				var_dump($this);
				echo '</pre>';
			}

			throw new bPostException($errorMessage, $errorNumber);
		}

		if(!in_array($headers['http_code'], array(0, 200)))
		{
			// internal debugging enabled
			if(self::DEBUG)
			{
				echo '<pre>';
				var_dump(htmlentities($response));
				var_dump($this);
				echo '</pre>';
			}

			throw new bPostException('invalid response', $headers['http_code']);
		}

		$xml = simplexml_load_string($response);

		// validate
		if($xml->getName() == 'businessException')
		{
			// internal debugging enabled
			if(self::DEBUG)
			{
				echo '<pre>';
				var_dump(htmlentities($body));
				var_dump($response);
				var_dump($headers);
				var_dump($this);
				echo '</pre>';
			}

			// message
			$message = (string) $response->Message;
			$code = (string) $response->Code;

			// throw exception
			throw new bPostException($message, $code);
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
		return base64_encode($this->accountId . ':' . $this->passphrase);
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
	 * @param int $seconds	The timeout in seconds.
	 */
	public function setTimeOut($seconds)
	{
		$this->timeOut = (int) $seconds;
	}

	/**
	 * Set the user-agent for you application
	 * It will be appended to ours, the result will look like: "PHP bPost/<version> <your-user-agent>"
	 *
	 * @param string $userAgent	Your user-agent, it should look like <app-name>/<app-version>.
	 */
	public function setUserAgent($userAgent)
	{
		$this->userAgent = (string) $userAgent;
	}

	// webservice methods
	public function createOrReplaceOrder()
	{

	}

	/**
	 * Fetch an order
	 *
	 * @param $reference
	 * @return array
	 */
	public function fetchOrder($reference)
	{
		// build url
		$url = '/orders/' . (string) $reference;

		$response = self::decodeResponse($this->doCall($url));

		Spoon::dump($response);

	}
}

/**
 * bPost Exception class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class bPostException extends Exception
{}