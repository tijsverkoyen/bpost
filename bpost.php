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
	 * @param DOMDocument $xml		Some data.
	 */
	private static function arrayToXML(&$input, $key, $xml)
	{
		// skip attributes
		if($key == '@attributes') return;

		if(is_null($input)) return;

		// create element
		$element = new DOMElement($key);

		// append
		$xml->appendChild($element);

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
			if($isNonNumeric) array_walk($input, array('bPost', 'arrayToXML'), $element);

			// numeric elements means this a list of items
			else
			{
				// handle the value as an element
				foreach($input as $value)
				{
					if(is_array($value)) array_walk($value, array('bPost', 'arrayToXML'), $element);
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
	private static function decodeResponse($item, $return = null, $i = 0)
	{
		$arrayKeys = array('barcode');
		$integerKeys = array('totalPrice');

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
					if(in_array($key, $arrayKeys)) $return[$key][] = (string) $value;

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
	private function doCall($url, $data = null, $headers = array(), $method = 'GET', $expectXML = true)
	{
		// any data?
		if($data !== null)
		{
			// init XML
			$xml = new DOMDocument('1.0', 'utf-8');

			// set some properties
			$xml->preserveWhiteSpace = false;
			$xml->formatOutput = true;

			// build data
			array_walk($data, array(__CLASS__, 'arrayToXML'), $xml);

			// store body
			$body = $xml->saveXML();
		}
		else $body = null;

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
		if($method == 'PUT')
		{
			$options[CURLOPT_CUSTOMREQUEST] = 'PUT';
			if($body != null) $options[CURLOPT_POSTFIELDS] = $body;
		}
		if($method == 'POST')
		{
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

		// valid HTTP-code
		if(!in_array($headers['http_code'], array(0, 200)))
		{
			// internal debugging enabled
			if(self::DEBUG)
			{
				echo '<pre>';
				var_dump($response);
				var_dump($headers);
				var_dump($this);
				echo '</pre>';
			}

			throw new bPostException('invalid response', $headers['http_code']);
		}

		// if we don't expect XML we can return the content here
		if(!$expectXML) return $response;

		// convert into XML
		$xml = simplexml_load_string($response);

		// validate
		if($xml->getName() == 'businessException')
		{
			// internal debugging enabled
			if(self::DEBUG)
			{
				echo '<pre>';
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
// orders
	public function createOrReplaceOrder()
	{
		throw new bPostException('Not implemented');
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

		// make the call
		return self::decodeResponse($this->doCall($url));
	}

	/**
	 * Modify the status for an order.
	 *
	 * @param string $reference		The reference for an order
	 * @param string $status		The new status, allowed values are: OPEN, PENDING, CANCELLED, COMPLETED or ON-HOLD
	 * @return bool
	 */
	public function modifyOrderStatus($reference, $status)
	{
		$allowedStatuses = array('OPEN', 'PENDING', 'CANCELLED', 'COMPLETED', 'ON-HOLD');
		$status = mb_strtoupper((string) $status);

		// validate
		if(!in_array($status, $allowedStatuses))
		{
			throw new bPostException('Invalid status (' . $status . '), allowed values are: ' . implode(', ', $allowedStatuses) . '.');
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
	public function createNationalLabel($reference, $amount, $withRetour = null, $returnLabels = null, $labelFormat = null)
	{
	}

	public function createInternationalLabel()
	{
		throw new bPostException('Not implemented');
	}

	public function createOrderAndNationalLabel()
	{
		throw new bPostException('Not implemented');
	}

	public function createOrderAndInternationalLabel()
	{
		throw new bPostException('Not implemented');
	}

	public function retrievePDFLabelsForBox()
	{
		throw new bPostException('Not implemented');
	}

	public function retrievePDFLabelsForOrder()
	{
		throw new bPostException('Not implemented');
	}
}

/**
 * bPost Exception class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class bPostException extends Exception
{}