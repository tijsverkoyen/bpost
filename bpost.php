<?php

/**
 * bPost class
 *
 * This source file can be used to communicate with the bPost Shipping Manager API
 *
 * The class is documented in the file itself. If you find any bugs help me out and report them. Reporting can be done by sending an email to php-bpost-bugs[at]verkoyen[dot]eu.
 * If you report a bug, make sure you give me enough information (include your code).
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
class bPost
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
		// wierd stuff
		if(in_array($key, array('orderLine', 'internationalLabelInfo')))
		{
			foreach($input as $row)
			{
				$element = new DOMElement($key);
				$xml->appendChild($element);

				// loop properties
				foreach($row as $name => $value)
				{
					if(is_bool($value)) $value = ($value) ? 'true' : 'false';

					$node = new DOMElement($name, $value);
					$element->appendChild($node);
				}
			}

			return;
		}

		// skip attributes
		if($key == '@attributes') return;

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

			// reset value
			if(count($input) == 2 && isset($input['value'])) $input = $input['value'];

			// reset the input if it is a single value
			elseif(count($input) == 1) return;
		}

		// the input isn't an array
		if(!is_array($input))
		{
			// boolean
			if(is_bool($input)) $element->appendChild(new DOMText(($input) ? 'true' : 'false'));

			// is_numeric
			elseif(is_numeric($input)) $element->appendChild(new DOMText($input));

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
		$arrayKeys = array('barcode', 'orderLine', 'additionalInsurance', 'infoDistributed', 'infoPugo');
		$integerKeys = array('totalPrice');

		if($item instanceof SimpleXMLElement)
		{
			foreach($item as $key => $value)
			{
				$attributes = (array) $value->attributes();

				if(!empty($attributes) && isset($attributes['@attributes']))
				{
					$return[$key]['@attributes'] = $attributes['@attributes'];
				}

				// empty
				if(isset($value['nil']) && (string) $value['nil'] === 'true') $return[$key] = null;

				// empty
				elseif(isset($value[0]) && (string) $value == '')
				{
					if(in_array($key, $arrayKeys))
					{
						$return[$key][] = self::decodeResponse($value);
					}

					else $return[$key] = self::decodeResponse($value, null, 1);
				}

				else
				{
					// arrays
					if(in_array($key, $arrayKeys))
					{
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
		}

		else throw new bPostException('Invalid item.');

		return $return;
	}

	/**
	 * Make the call
	 *
	 * @param string $url					The URL to call.
	 * @param array[optional] $data			The data to pass.
	 * @param array[optional] $headers		The headers to pass.
	 * @param string[optional] $method		The HTTP-method to use.
	 * @param bool[optional] $expectXML		Do we expect XML?
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
				var_dump(htmlentities($body));
				var_dump($response);
				var_dump($headers);
				var_dump($this);
				echo '</pre>';
			}

			throw new bPostException('Invalid response.', $headers['http_code']);
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
	/**
	 * Creates a new order. If an order with the same orderReference already exists
	 *
	 * @param bPostOrder $order
	 * @return bool
	 */
	public function createOrReplaceOrder(bPostOrder $order)
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
	 * @param string $reference
	 * @return array
	 */
	public function fetchOrder($reference)
	{
		// build url
		$url = '/orders/' . (string) $reference;

		// make the call
		$return = self::decodeResponse($this->doCall($url));

		// for some reason the order-data is wrapped in an order tag sometimes.
		if(isset($return['order']))
		{
			if(isset($return['barcode'])) $barcodes = $return['barcode'];
			$return = $return['order'];
		}

		$order = new bPostOrder($return['orderReference']);

		if(isset($barcodes)) $order->setBarcodes($barcodes);

		if(isset($return['status'])) $order->setStatus($return['status']);
		if(isset($return['costCenter'])) $order->setCostCenter($return['costCenter']);

		// order lines
		if(isset($return['orderLine']) && !empty($return['orderLine']))
		{
			foreach($return['orderLine'] as $row)
			{
				$order->addOrderLine($row['text'], $row['nbOfItems']);
			}
		}

		// customer
		if(isset($return['customer']))
		{
			// create customer
			$customer = new bPostCustomer($return['customer']['firstName'], $return['customer']['lastName']);
			if(isset($return['customer']['deliveryAddress']))
			{
				$address = new bPostAddress(
					$return['customer']['deliveryAddress']['streetName'],
					$return['customer']['deliveryAddress']['number'],
					$return['customer']['deliveryAddress']['postalCode'],
					$return['customer']['deliveryAddress']['locality'],
					$return['customer']['deliveryAddress']['countryCode']
				);
				if(isset($return['customer']['deliveryAddress']['box']))
				{
					$address->setBox($return['customer']['deliveryAddress']['box']);
				}
				$customer->setDeliveryAddress($address);
			}
			if(isset($return['customer']['email'])) $customer->setEmail($return['customer']['email']);
			if(isset($return['customer']['phoneNumber'])) $customer->setPhoneNumber($return['customer']['phoneNumber']);

			$order->setCustomer($customer);
		}

		// delivery method
		if(isset($return['deliveryMethod']))
		{
			// atHome?
			if(isset($return['deliveryMethod']['atHome']))
			{
				$deliveryMethod = new bPostDeliveryMethodAtHome();

				// options
				if(isset($return['deliveryMethod']['atHome']['normal']['options']) && !empty($return['deliveryMethod']['atHome']['normal']['options']))
				{
					$options = array();

					foreach($return['deliveryMethod']['atHome']['normal']['options'] as $key => $row)
					{
						$language = 'NL';	// @todo fix me
						$emailAddress = null;
						$mobilePhone = null;
						$fixedPhone = null;

						if(isset($row['emailAddress'])) $emailAddress = $row['emailAddress'];
						if(isset($row['mobilePhone'])) $mobilePhone = $row['mobilePhone'];
						if(isset($row['fixedPhone'])) $fixedPhone = $row['fixedPhone'];

						if($emailAddress === null && $mobilePhone === null && $fixedPhone === null) continue;

						$options[$key] = new bPostNotification($language, $emailAddress, $mobilePhone, $fixedPhone);
					}

					$deliveryMethod->setNormal($options);
				}

				$order->setDeliveryMethod($deliveryMethod);
			}

			// atShop
			elseif(isset($return['deliveryMethod']['atShop']))
			{
				$deliveryMethod = new bPostDeliveryMethodAtShop();

				$language = $return['deliveryMethod']['atShop']['infoPugo']['@attributes']['language'];
				$emailAddress = null;
				$mobilePhone = null;
				$fixedPhone = null;

				if(isset($return['deliveryMethod']['atShop']['infoPugo'][0]['emailAddress']))
				{
					$emailAddress = $return['deliveryMethod']['atShop']['infoPugo'][0]['emailAddress'];
				}
				if(isset($return['deliveryMethod']['atShop']['infoPugo'][0]['mobilePhone']))
				{
					$mobilePhone = $return['deliveryMethod']['atShop']['infoPugo'][0]['mobilePhone'];
				}
				if(isset($return['deliveryMethod']['atShop']['infoPugo'][0]['fixedPhone']))
				{
					$fixedPhone = $return['deliveryMethod']['atShop']['infoPugo'][0]['fixedPhone'];
				}

				$deliveryMethod->setInfoPugo(
					$return['deliveryMethod']['atShop']['infoPugo'][0]['pugoId'],
					$return['deliveryMethod']['atShop']['infoPugo'][0]['pugoName'],
					new bPostNotification($language, $emailAddress, $mobilePhone, $fixedPhone)
				);

				if(isset($return['deliveryMethod']['atShop']['insurance']['additionalInsurance']['@attributes']['value']))
				{
					$deliveryMethod->setInsurance((int) $return['deliveryMethod']['atShop']['insurance']['additionalInsurance']['@attributes']['value']);
				}

				$language = $return['deliveryMethod']['atShop']['infoPugo']['@attributes']['language'];
				$emailAddress = null;
				$mobilePhone = null;
				$fixedPhone = null;
				$pugoId = null;
				$pugoName = null;

				if(isset($return['deliveryMethod']['atShop']['infoPugo'][0]['emailAddress']))
				{
					$emailAddress = $return['deliveryMethod']['atShop']['infoPugo'][0]['emailAddress'];
				}
				if(isset($return['deliveryMethod']['atShop']['infoPugo'][0]['mobilePhone']))
				{
					$mobilePhone = $return['deliveryMethod']['atShop']['infoPugo'][0]['mobilePhone'];
				}
				if(isset($return['deliveryMethod']['atShop']['infoPugo'][0]['fixedPhone']))
				{
					$fixedPhone = $return['deliveryMethod']['atShop']['infoPugo'][0]['fixedPhone'];
				}
				if(isset($return['deliveryMethod']['atShop']['infoPugo'][0]['pugoId']))
				{
					$pugoId = $return['deliveryMethod']['atShop']['infoPugo'][0]['pugoId'];
				}
				if(isset($return['deliveryMethod']['atShop']['infoPugo'][0]['pugoName']))
				{
					$pugoName = $return['deliveryMethod']['atShop']['infoPugo'][0]['pugoName'];
				}

				$deliveryMethod->setInfoPugo(
					$pugoId, $pugoName,
					new bPostNotification($language, $emailAddress, $mobilePhone, $fixedPhone)
				);

				$order->setDeliveryMethod($deliveryMethod);
			}

			// at24-7
			elseif(isset($return['deliveryMethod']['at24-7']))
			{
				$deliveryMethod = new bPostDeliveryMethodAt247(
					$return['deliveryMethod']['at24-7']['infoParcelsDepot']['parcelsDepotId']
				);
				if(isset($return['deliveryMethod']['at24-7']['memberId']))
				{
					$deliveryMethod->setMemberId($return['deliveryMethod']['at24-7']['memberId']);
				}
				if(isset($return['deliveryMethod']['at24-7']['signature']['signature']))
				{
					$deliveryMethod->setSignature();
				}
				if(isset($return['deliveryMethod']['at24-7']['signature']['signature']))
				{
					$deliveryMethod->setSignature(true);
				}
				if(isset($return['deliveryMethod']['at24-7']['insurance']['additionalInsurance']['@attributes']['value']))
				{
					$deliveryMethod->setInsurance((int) $return['deliveryMethod']['at24-7']['insurance']['additionalInsurance']['@attributes']['value']);
				}

				$order->setDeliveryMethod($deliveryMethod);
			}

			// intExpress?
			elseif(isset($return['deliveryMethod']['intExpress']))
			{
				$deliveryMethod = new bPostDeliveryMethodIntBusiness();

				if(isset($return['deliveryMethod']['intExpress']['insured']['additionalInsurance']['@attributes']['value']))
				{
					$deliveryMethod->setInsurance((int) $return['deliveryMethod']['intExpress']['insured']['additionalInsurance']['@attributes']['value']);
				}

				// options
				if(isset($return['deliveryMethod']['intBusiness']['insured']['options']) && !empty($return['deliveryMethod']['intExpress']['insured']['options']))
				{
					$options = array();

					foreach($return['deliveryMethod']['intExpress']['insured']['options'] as $key => $row)
					{
						$language = 'NL';	// @todo fix me
						$emailAddress = null;
						$mobilePhone = null;
						$fixedPhone = null;

						if(isset($row['emailAddress'])) $emailAddress = $row['emailAddress'];
						if(isset($row['mobilePhone'])) $mobilePhone = $row['mobilePhone'];
						if(isset($row['fixedPhone'])) $fixedPhone = $row['fixedPhone'];

						if($emailAddress === null && $mobilePhone === null && $fixedPhone === null) continue;

						$options[$key] = new bPostNotification($language, $emailAddress, $mobilePhone, $fixedPhone);
					}

					$deliveryMethod->setInsured($options);
				}

				$order->setDeliveryMethod($deliveryMethod);
			}

			// intBusiness?
			elseif(isset($return['deliveryMethod']['intBusiness']))
			{
				$deliveryMethod = new bPostDeliveryMethodIntBusiness();

				if(isset($return['deliveryMethod']['intBusiness']['insured']['additionalInsurance']['@attributes']['value']))
				{
					$deliveryMethod->setInsurance((int) $return['deliveryMethod']['intBusiness']['insured']['additionalInsurance']['@attributes']['value']);
				}

				// options
				if(isset($return['deliveryMethod']['intBusiness']['insured']['options']) && !empty($return['deliveryMethod']['intBusiness']['insured']['options']))
				{
					$options = array();

					foreach($return['deliveryMethod']['intBusiness']['insured']['options'] as $key => $row)
					{
						$language = 'NL';	// @todo fix me
						$emailAddress = null;
						$mobilePhone = null;
						$fixedPhone = null;

						if(isset($row['emailAddress'])) $emailAddress = $row['emailAddress'];
						if(isset($row['mobilePhone'])) $mobilePhone = $row['mobilePhone'];
						if(isset($row['fixedPhone'])) $fixedPhone = $row['fixedPhone'];

						if($emailAddress === null && $mobilePhone === null && $fixedPhone === null) continue;

						$options[$key] = new bPostNotification($language, $emailAddress, $mobilePhone, $fixedPhone);
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
			throw new bPostException(
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
	 * @param string $reference					Order reference: unique ID used in your web shop to assign to an order.
	 * @param int $amount						Amount of labels.
	 * @param bool[optional] $withRetour		Should the return labeks be included?
	 * @param bool[optional] $returnLabels		Should the labels be included?
	 * @param string[optional] $labelFormat		Format of the labels, possible values are: A_4, A_5.
	 * @return array
	 */
	public function createNationalLabel($reference, $amount, $withRetour = null, $returnLabels = null, $labelFormat = null)
	{
		$allowedLabelFormats = array('A_4', 'A_5');

		// validate
		if($labelFormat !== null && !in_array($labelFormat, $allowedLabelFormats))
		{
			throw new bPostException(
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
		if(!isset($return['entry'])) throw new bPostException('Invalid response.');

		// return
		return $return['entry'];
	}

	/**
	 * Create an international label
	 *
	 * @param string $reference									Order reference: unique ID used in your web shop to assign to an order.
	 * @param array $labelInfo	For each label an object should be present
	 * @param bool[optional] $returnLabels						Should the labels be included?
	 * @return array
	 */
	public function createInternationalLabel($reference, array $labelInfo, $returnLabels = null)
	{
		// build url
		$url = '/labels';

		// build data
		$data['internationalLabelInfos']['@attributes']['xmlns'] = 'http://schema.post.be/shm/deepintegration/v2/';

		foreach($labelInfo as $row)
		{
			if(!($row instanceof bPostInternationalLabelInfo))
			{
				throw new bPostException(
					'Invalid value for labelInfo, should be an instance of bPostInternationalLabelInfo'
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
		if(!isset($return['entry'])) throw new bPostException('Invalid response.');

		// return
		return $return['entry'];
	}

	/**
	 * Create an order and the labels
	 *
	 * @param bPostOrder $order
	 * @param int $amount
	 * @return array
	 */
	public function createOrderAndNationalLabel(bPostOrder $order, $amount)
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
		if(!isset($return['entry'])) throw new bPostException('Invalid response.');

		// return
		return $return['entry'];
	}

	/**
	 * Create an order and an international label
	 *
	 * @param array $labelInfo		The label info
	 * @param bpostOrder $order		The order
	 * @return array
	 */
	public function createOrderAndInternationalLabel(array $labelInfo, bPostOrder $order)
	{
		// build url
		$url = '/orderAndLabels';

		// build data
		$data['orderInternationalLabelInfos']['@attributes']['xmlns'] = 'http://schema.post.be/shm/deepintegration/v2/';
		foreach($labelInfo as $row)
		{
			if(!($row instanceof bPostInternationalLabelInfo))
			{
				throw new bPostException(
					'Invalid value for labelInfo, should be an instance of bPostInternationalLabelInfo'
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
		if(!isset($return['entry'])) throw new bPostException('Invalid response.');

		// return
		return $return['entry'];
	}

	/**
	 * Retrieve a PDF-label for a box
	 *
	 * @param string $barcode					The barcode to retrieve
	 * @param string[optional] $labelFormat		Possible values are: A_4, A_5
	 * @return string
	 */
	public function retrievePDFLabelsForBox($barcode, $labelFormat = null)
	{
		$allowedLabelFormats = array('A_4', 'A_5');

		// validate
		if($labelFormat !== null && !in_array($labelFormat, $allowedLabelFormats))
		{
			throw new bPostException(
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
	 * @param string $reference
	 * @param string[optional] $labelFormat		Possible values are: A_4, A_5
	 * @return string
	 */
	public function retrievePDFLabelsForOrder($reference, $labelFormat = null)
	{
		$allowedLabelFormats = array('A_4', 'A_5');

		// validate
		if($labelFormat !== null && !in_array($labelFormat, $allowedLabelFormats))
		{
			throw new bPostException(
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

/**
 * bPost Order class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class bPostOrder
{
	/**
	 * Generic info
	 *
	 * @var string
	 */
	private $costCenter, $status, $reference;

	/**
	 * The order lines
	 * @var array
	 */
	private $lines, $barcodes;

	/**
	 * The customer
	 *
	 * @var bPostCustomer
	 */
	private $customer;

	/**
	 * The delivery method
	 *
	 * @var bPostDeliveryMethod
	 */
	private $deliveryMethod;

	/**
	 * The order total
	 *
	 * @var int
	 */
	private $total;

	/**
	 * Create an order
	 *
	 * @param string $reference
	 */
	public function __construct($reference)
	{
		$this->setReference($reference);
	}

	/**
	 * Add an order line
	 *
	 * @param string $text			Text describing the ordered item.
	 * @param int $numberOfItems	Number of items.
	 */
	public function addOrderLine($text, $numberOfItems)
	{
		$this->lines[] = array(
			'text' => (string) $text,
			'nbOfItems' => (int) $numberOfItems
		);
	}

	/**
	 * Get the barcodes
	 *
	 * @return array
	 */
	public function getBarcodes()
	{
		return $this->barcodes;
	}

	/**
	 * Get the cost center
	 * @return string
	 */
	public function getCostCenter()
	{
		return $this->costCenter;
	}

	/**
	 * Get the customer
	 *
	 * @return bPostCustomer
	 */
	public function getCustomer()
	{
		return $this->customer;
	}

	/**
	 * Get the delivery method
	 *
	 * @return bPostDeliveryMethod
	 */
	public function getDeliveryMethod()
	{
		return $this->deliveryMethod;
	}

	/**
	 * Get the order lines
	 *
	 * @return array
	 */
	public function getOrderLines()
	{
		return $this->lines;
	}

	/**
	 * Get the reference
	 *
	 * @return string
	 */
	public function getReference()
	{
		return $this->reference;
	}

	/**
	 * Get the status
	 *
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Get the total price of the order.
	 *
	 * @return int
	 */
	public function getTotal()
	{
		return $this->total;
	}

	/**
	 * Set the barcodes
	 *
	 * @param array $barcodes
	 */
	public function setBarcodes(array $barcodes)
	{
		$this->barcodes = $barcodes;
	}

	/**
	 * Set teh cost center, will be used on your invoice and allows you to attribute different cost centers
	 *
	 * @param string $costCenter
	 */
	public function setCostCenter($costCenter)
	{
		$this->costCenter = (string) $costCenter;
	}

	/**
	 * Set the customer
	 *
	 * @param bPostCustomer $customer
	 */
	public function setCustomer(bPostCustomer $customer)
	{
		$this->customer = $customer;
	}

	/**
	 * Set the delivery method
	 *
	 * @param bPostDeliveryMethod $deliveryMethod
	 */
	public function setDeliveryMethod(bPostDeliveryMethod $deliveryMethod)
	{
		$this->deliveryMethod = $deliveryMethod;
	}

	/**
	 * Set the order reference, a unique id used in your web-shop.
	 * If the value already exists it will overwrite the current info.
	 *
	 * @param string $reference
	 */
	public function setReference($reference)
	{
		$this->reference = (string) $reference;
	}

	/**
	 * Set the order status
	 *
	 * @param string $status	Possible values are OPEN, PENDING, CANCELLED, COMPLETED, ON-HOLD.
	 */
	public function setStatus($status)
	{
		$allowedStatuses = array('OPEN', 'PENDING', 'CANCELLED', 'COMPLETED', 'ON-HOLD');

		// validate
		if(!in_array($status, $allowedStatuses))
		{
			throw new bPostException(
				'Invalid status (' . $status . '), possible values are: ' . implode(', ', $allowedStatuses) . '.'
			);
		}

		$this->status = $status;
	}

	/**
	 * The total price of the order in euro-cents (excluding shipping)
	 *
	 * @param int $total
	 */
	public function setTotal($total)
	{
		$this->total = (int) $total;
	}

	/**
	 * Return the object as an array for usage in the XML
	 *
	 * @param string $accountId
	 * @return array
	 */
	public function toXMLArray($accountId)
	{
		$data = array();
		$data['@attributes']['xmlns'] = 'http://schema.post.be/shm/deepintegration/v2/';
		$data['accountId'] = (string) $accountId;
		if($this->reference !== null) $data['orderReference'] = $this->reference;
		if($this->status !== null) $data['status'] = $this->status;
		if($this->costCenter !== null) $data['costCenter'] = $this->costCenter;

		if(!empty($this->lines))
		{
			foreach($this->lines as $line)
			{
				$data['orderLine'][] = $line;
			}
		}

		if($this->customer !== null) $data['customer'] = $this->customer->toXMLArray();
		if($this->deliveryMethod !== null) $data['deliveryMethod'] = $this->deliveryMethod->toXMLArray();
		if($this->total !== null) $data['totalPrice'] = $this->total;

		return $data;
	}
}

/**
 * bPost Customer class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class bPostCustomer
{
	/**
	 * Generic info
	 *
	 * @var string
	 */
	private $firstName, $lastName, $company, $email, $phoneNumber;

	/**
	 * The address
	 *
	 * @var bPostAddress
	 */
	private $deliveryAddress;

	/**
	 * Create a customer
	 *
	 * @param string $firstName
	 * @param string $lastName
	 */
	public function __construct($firstName, $lastName)
	{
		$this->setFirstName($firstName);
		$this->setLastName($lastName);
	}

	/**
	 * Get the delivery address
	 *
	 * @return bPostAddress
	 */
	public function getDeliveryAddress()
	{
		return $this->deliveryAddress;
	}

	/**
	 * Get the email
	 *
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Get the first name
	 *
	 * @return string
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}

	/**
	 * Get the last name
	 *
	 * @return string
	 */
	public function getLastName()
	{
		return $this->lastName;
	}

	/**
	 * Get the company
	 *
	 * @return string
	 */
	public function getCompany()
	{
		return $this->company;
	}

	/**
	 * Get the phone number
	 *
	 * @return string
	 */
	public function getPhoneNumber()
	{
		return $this->phoneNumber;
	}

	/**
	 * Set the delivery address
	 *
	 * @param bPostAddress $deliveryAddress
	 */
	public function setDeliveryAddress($deliveryAddress)
	{
		$this->deliveryAddress = $deliveryAddress;
	}

	/**
	 * Set the email
	 *
	 * @param string $email
	 */
	public function setEmail($email)
	{
		if(mb_strlen($email) > 50) throw new bPostException('Invalid length for email, maximum is 50.');
		$this->email = $email;
	}

	/**
	 * Set the first name
	 *
	 * @param string $firstName
	 */
	public function setFirstName($firstName)
	{
		if(mb_strlen($firstName) > 40) throw new bPostException('Invalid length for firstName, maximum is 40.');
		$this->firstName = $firstName;
	}

	/**
	 * Set the last name
	 *
	 * @param string $lastName
	 */
	public function setLastName($lastName)
	{
		if(mb_strlen($lastName) > 40) throw new bPostException('Invalid length for lastName, maximum is 40.');
		$this->lastName = $lastName;
	}

	/**
	 * Set the company
	 *
	 * @param string $lastName
	 */
	public function setCompany($company)
	{
		if(mb_strlen($company) > 40) throw new bPostException('Invalid length for company, maximum is 40.');
		$this->company = $company;
	}

	/**
	 * Set the phone number
	 *
	 * @param string $phoneNumber
	 */
	public function setPhoneNumber($phoneNumber)
	{
		if(mb_strlen($phoneNumber) > 20) throw new bPostException('Invalid length for phone number, maximum is 20.');
		$this->phoneNumber = $phoneNumber;
	}

	/**
	 * Return the object as an array for usage in the XML
	 *
	 * @return array
	 */
	public function toXMLArray()
	{
		$data = array();
		if($this->firstName !== null) $data['firstName'] = $this->firstName;
		if($this->lastName !== null) $data['lastName'] = $this->lastName;
		if($this->company !== null) $data['company'] = $this->company;
		if($this->deliveryAddress !== null) $data['deliveryAddress'] = $this->deliveryAddress->toXMLArray();
		if($this->email !== null) $data['email'] = $this->email;
		if($this->phoneNumber !== null) $data['phoneNumber'] = $this->phoneNumber;

		return $data;
	}
}

/**
 * bPost Address class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class bPostAddress
{
	/**
	 * Generic info
	 *
	 * @var string
	 */
	private $streetName, $number, $box, $postcalCode, $locality, $countryCode;

	/**
	 * Create a Address object
	 *
	 * @param string $streetName
	 * @param string $number
	 * @param string $postalCode
	 * @param string $locality
	 * @param string[optional] $countryCode
	 */
	public function __construct($streetName, $number, $postalCode, $locality, $countryCode = 'BE')
	{
		$this->setStreetName($streetName);
		$this->setNumber($number);
		$this->setPostcalCode($postalCode);
		$this->setLocality($locality);
		$this->setCountryCode($countryCode);
	}

	/**
	 * Get the box
	 *
	 * @return string
	 */
	public function getBox()
	{
		return $this->box;
	}

	/**
	 * Get the country code
	 *
	 * @return string
	 */
	public function getCountryCode()
	{
		return $this->countryCode;
	}

	/**
	 * Get the locality
	 *
	 * @return string
	 */
	public function getLocality()
	{
		return $this->locality;
	}

	/**
	 * Get the number
	 *
	 * @return string
	 */
	public function getNumber()
	{
		return $this->number;
	}

	/**
	 * Get the postal code
	 *
	 * @return string
	 */
	public function getPostcalCode()
	{
		return $this->postcalCode;
	}

	/**
	 * Get the street name
	 *
	 * @return string
	 */
	public function getStreetName()
	{
		return $this->streetName;
	}

	/**
	 * Set the box
	 *
	 * @param string $box
	 */
	public function setBox($box)
	{
		if(mb_strlen($box) > 8) throw new bPostException('Invalid length for box, maximum is 8.');
		$this->box = $box;
	}

	/**
	 * Set the country code
	 *
	 * @param string $countryCode
	 */
	public function setCountryCode($countryCode)
	{
		$this->countryCode = $countryCode;
	}

	/**
	 * Set the locality
	 *
	 * @param string $locality
	 */
	public function setLocality($locality)
	{
		if(mb_strlen($locality) > 40) throw new bPostException('Invalid length for locality, maximum is 40.');
		$this->locality = $locality;
	}

	/**
	 * Set the number
	 *
	 * @param string $number
	 */
	public function setNumber($number)
	{
		if(mb_strlen($number) > 8) throw new bPostException('Invalid length for number, maximum is 8.');
		$this->number = $number;
	}

	/**
	 * Set the postal code
	 *
	 * @param string $postcalCode
	 */
	public function setPostcalCode($postcalCode)
	{
		if(mb_strlen($postcalCode) > 8) throw new bPostException('Invalid length for postalCode, maximum is 8.');
		$this->postcalCode = $postcalCode;
	}

	/**
	 * Set the street name
	 * @param string $streetName
	 */
	public function setStreetName($streetName)
	{
		if(mb_strlen($streetName) > 40) throw new bPostException('Invalid length for streetName, maximum is 40.');
		$this->streetName = $streetName;
	}

	/**
	 * Return the object as an array for usage in the XML
	 *
	 * @return array
	 */
	public function toXMLArray()
	{
		$data = array();
		if($this->streetName !== null) $data['streetName'] = $this->streetName;
		if($this->number !== null) $data['number'] = $this->number;
		if($this->box !== null) $data['box'] = $this->box;
		if($this->postcalCode !== null) $data['postalCode'] = $this->postcalCode;
		if($this->locality !== null) $data['locality'] = $this->locality;
		if($this->countryCode !== null) $data['countryCode'] = $this->countryCode;

		return $data;
	}
}

/**
 * bPost Delivery Method class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class bPostDeliveryMethod
{
	protected $insurance;

	/**
	 * Set the insurance level
	 *
	 * @param int $level	Level from 0 to 11.
	 */
	public function setInsurance($level = 0)
	{
		if((int) $level > 11) throw new bPostException('Invalid value () for level.');
		$this->insurance = $level;
	}

	/**
	 * Return the object as an array for usage in the XML
	 *
	 * @return array
	 */
	public function toXMLArray()
	{
		// build data
		$data = array();
		if($this->insurance !== null)
		{
			if($this->insurance == 0) $data['insurance']['basicInsurance'] = '';
			else $data['insurance']['additionalInsurance']['@attributes']['value'] = $this->insurance;
		}

		return $data;
	}
}

/**
 * bPost Delivery At Home Method class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class bPostDeliveryMethodAtHome extends bPostDeliveryMethod
{
	private $normal, $insured;

	/**
	 * @var bool
	 */
	private $dropAtTheDoor;

	/**
	 * Get the options
	 *
	 * @return array
	 */
	public function getInsured()
	{
		return $this->insured;
	}

	/**
	 * Get the options
	 *
	 * @return mixed
	 */
	public function getNormal()
	{
		return $this->normal;
	}

	/**
	 * Set drop at the door
	 *
	 * @param bool $dropAtTheDoor
	 */
	public function setDropAtTheDoor($dropAtTheDoor = true)
	{
		$this->dropAtTheDoor = (bool) $dropAtTheDoor;
	}

	/**
	 * Set normal
	 *
	 * @param array $options
	 */
	public function setNormal(array $options = null)
	{
		if($options !== null)
		{
			foreach($options as $key => $value) $this->normal[$key] = $value;
		}
		else $this->normal = array();
	}

	/**
	 * Return the object as an array for usage in the XML
	 *
	 * @return array
	 */
	public function toXMLArray()
	{
		$data = array();
		if($this->normal !== null)
		{
			$data['atHome']['normal'] = null;

			foreach($this->normal as $key => $value)
			{
				if($key == 'automaticSecondPresentation') $data['atHome']['normal']['options']['automaticSecondPresentation'] = $value;
				else $data['atHome']['normal']['options'][$key] = $value->toXMLArray();
			}
		}
		if($this->insurance !== null)
		{
			if($this->insurance == 0) $data['atHome-7']['insurance']['basicInsurance'] = '';
			else $data['atHome']['insurance']['additionalInsurance']['@attributes']['value'] = $this->insurance;
		}
		if($this->dropAtTheDoor) $data['atHome']['dropAtTheDoor'] = null;

		return $data;
	}
}

/**
 * bPost Delivery At Shop Method class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class bPostDeliveryMethodAtShop extends bPostDeliveryMethod
{
	/**
	 * Generic Info
	 *
	 * @var mixed
	 */
	private $infoPugo, $infoDistributed;

	/**
	 * Get the options
	 *
	 * @return array
	 */
	public function getInfoDistributed()
	{
		return $this->infoDistributed;
	}

	/**
	 * Get the info pigu
	 *
	 * @return mixed
	 */
	public function getInfoPugo()
	{
		return $this->infoPugo;
	}

	/**
	 * Set the options
	 *
	 * @param bPostNotification $notification
	 */
	public function setInfoDistributed(bPostNotification $notification)
	{
		$this->infoDistributed = $notification;
	}

	/**
	 * Set the Pick Up & Go information
	 *
	 * @param string $id						Id of the Pick Up & Go
	 * @param string $name						Name of the Pick Up & Go
	 * @param bPostNotification $notification	One of the notification tags.
	 */
	public function setInfoPugo($id, $name, bPostNotification $notification)
	{
		$this->infoPugo['pugoId'] = (string) $id;
		$this->infoPugo['pugoName'] = (string) $name;
		$this->infoPugo['notification'] = $notification;
	}

	/**
	 * Return the object as an array for usage in the XML
	 *
	 * @return array
	 */
	public function toXMLArray()
	{
		$data = array();
		if(isset($this->infoPugo['notification'])) $data['atShop']['infoPugo'] = $this->infoPugo['notification']->toXMLArray();
		$data['atShop']['infoPugo']['pugoId'] = $this->infoPugo['pugoId'];
		$data['atShop']['infoPugo']['pugoName'] = $this->infoPugo['pugoName'];

		if($this->insurance !== null)
		{
			if($this->insurance == 0) $data['atShop']['insurance']['basicInsurance'] = '';
			else $data['atShop']['insurance']['additionalInsurance']['@attributes']['value'] = $this->insurance;
		}
		if($this->infoDistributed !== null)
		{
			$data['atShop']['infoDistributed'] = $this->infoDistributed->toXMLArray();
		}

		return $data;
	}
}

/**
 * bPost Delivery At 24/7 Method class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class bPostDeliveryMethodAt247 extends bPostDeliveryMethod
{
	/**
	 * Generic info
	 *
	 * @var mixed
	 */
	private $infoParcelsDepot, $signature, $memberId;

	/**
	 * Create an at24-7 object
	 *
	 * @param string $parcelsDepotId
	 */
	public function __construct($parcelsDepotId)
	{
		$this->setInfoParcelsDepot($parcelsDepotId);
	}

	/**
	 * Get info parcel depot
	 *
	 * @return string
	 */
	public function getInfoParcelsDepot()
	{
		return $this->infoParcelsDepot;
	}

	/**
	 * Get member id
	 *
	 * @return mixed
	 */
	public function getMemberId()
	{
		return $this->memberId;
	}

	/**
	 * Get signature
	 *
	 * @return mixed
	 */
	public function getSignature()
	{
		return $this->signature;
	}

	/**
	 * Set info parcels depot
	 *
	 * @param string $infoParcelsDepot
	 */
	public function setInfoParcelsDepot($infoParcelsDepot)
	{
		$this->infoParcelsDepot = (string) $infoParcelsDepot;
	}

	/**
	 * Set member id
	 *
	 * @param string $memberId
	 */
	public function setMemberId($memberId)
	{
		$this->memberId = $memberId;
	}

	/**
	 * Set signature
	 *
	 * @param bool[optional] $isPlus
	 */
	public function setSignature($isPlus = false)
	{
		$this->signature = (bool) $isPlus;
	}

	/**
	 * Return the object as an array for usage in the XML
	 *
	 * @return array
	 */
	public function toXMLArray()
	{
		$data = array();
		$data['at24-7']['infoParcelsDepot']['parcelsDepotId'] = $this->infoParcelsDepot;
		$data['at24-7']['memberId'] = $this->memberId;
		if($this->signature !== null)
		{
			if($this->signature) $data['at24-7']['signaturePlus'] = null;
			else $data['at24-7']['signature'] = null;
		}
		if($this->insurance !== null)
		{
			if($this->insurance == 0) $data['at24-7']['insurance']['basicInsurance'] = '';
			else $data['at24-7']['insurance']['additionalInsurance']['@attributes']['value'] = $this->insurance;
		}

		return $data;
	}
}

/**
 * bPost Delivery International Express Method class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class bPostDeliveryMethodIntExpress extends bPostDeliveryMethod
{
	/**
	 * The options
	 *
	 * @var array
	 */
	private $insured;

	/**
	 * Get the options
	 *
	 * @return array
	 */
	public function getInsured()
	{
		return $this->insured;
	}

	/**
	 * Set the options
	 *
	 * @param array $options
	 */
	public function setInsured(array $options = null)
	{
		if($options !== null)
		{
			foreach($options as $key => $value) $this->insured[$key] = $value;
		}
	}

	/**
	 * Return the object as an array for usage in the XML
	 *
	 * @return array
	 */
	public function toXMLArray()
	{
		$data = array();
		$data['intExpress'] = null;
		if($this->insurance !== null)
		{
			if($this->insurance == 0) $data['intExpress']['insured']['basicInsurance'] = '';
			else $data['intExpress']['insured']['additionalInsurance']['@attributes']['value'] = $this->insurance;
		}
		if($this->insured !== null)
		{
			foreach($this->insured as $key => $value)
			{
				if($key == 'automaticSecondPresentation') $data['intExpress']['insured']['options']['automaticSecondPresentation'] = $value;
				else $data['intExpress']['insured']['options'][$key] = $value->toXMLArray();
			}
		}

		return $data;
	}

}

/**
 * bPost Delivery International Business Method class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class bPostDeliveryMethodIntBusiness extends bPostDeliveryMethod
{
	/**
	 * The options
	 *
	 * @var array
	 */
	private $insured;

	/**
	 * Get the options
	 *
	 * @return array
	 */
	public function getInsured()
	{
		return $this->insured;
	}

	/**
	 * Set the options
	 *
	 * @param array $options
	 */
	public function setInsured(array $options = null)
	{
		if($options !== null)
		{
			foreach($options as $key => $value) $this->insured[$key] = $value;
		}
	}

	/**
	 * Return the object as an array for usage in the XML
	 *
	 * @return array
	 */
	public function toXMLArray()
	{
		$data = array();
		$data['intBusiness'] = null;
		if($this->insurance !== null)
		{
			if($this->insurance == 0) $data['intBusiness']['insured']['basicInsurance'] = '';
			else $data['intBusiness']['insured']['additionalInsurance']['@attributes']['value'] = $this->insurance;
		}
		if($this->insured !== null)
		{
			foreach($this->insured as $key => $value)
			{
				if($key == 'automaticSecondPresentation') $data['intBusiness']['insured']['options']['automaticSecondPresentation'] = $value;
				else $data['intBusiness']['insured']['options'][$key] = $value->toXMLArray();
			}
		}
		return $data;
	}
}

/**
 * bPost International Label Info class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class bPostInternationalLabelInfo
{
	/**
	 * Generic info
	 *
	 * @var string
	 */
	private $contentDescription, $shipmentType, $parcelReturnInstructions;

	/**
	 * Generic info
	 *
	 * @var int
	 */
	private $parcelValue, $parcelWeight;

	/**
	 * Generic info
	 *
	 * @var bool
	 */
	private $privateAddress;

	/**
	 * @param int $parcelValue					The value of the parcel in euro cent
	 * @param int $parcelWeight					The weight of the parcel in grams
	 * @param string $contentDescription		The content description
	 * @param string $shipmentType				The shipment type, allowed values are: SAMPLE, GIFT, OTHER, DOCUMENT
	 * @param string $parcelReturnInstructions	The return instructions, allowed values are: RTA, ABANDONED, RTS
	 * @param bool[optional] $privateAddress	Is the address a private address?
	 */
	public function __construct($parcelValue, $parcelWeight, $contentDescription, $shipmentType, $parcelReturnInstructions, $privateAddress = true)
	{
		$this->setParcelValue($parcelValue);
		$this->setParcelWeight($parcelWeight);
		$this->setContentDescription($contentDescription);
		$this->setShipmentType($shipmentType);
		$this->setParcelReturnInstructions($parcelReturnInstructions);
		$this->setPrivateAddress($privateAddress);
	}

	/**
	 * Get the content description
	 *
	 * @return string
	 */
	public function getContentDescription()
	{
		return $this->contentDescription;
	}

	/**
	 * Get the parcel return instructions
	 *
	 * @return string
	 */
	public function getParcelReturnInstructions()
	{
		return $this->parcelReturnInstructions;
	}

	/**
	 * Get the parcel value in euro cents
	 *
	 * @return string
	 */
	public function getParcelValue()
	{
		return $this->parcelValue;
	}

	/**
	 * Get the parcel weight in grams
	 *
	 * @return int
	 */
	public function getParcelWeight()
	{
		return $this->parcelWeight;
	}

	/**
	 * Is the address a private address?
	 *
	 * @return bool
	 */
	public function getPrivateAddress()
	{
		return $this->privateAddress;
	}

	/**
	 * Get the shipment type
	 *
	 * @return string
	 */
	public function getShipmentType()
	{
		return $this->shipmentType;
	}

	/**
	 * Get the content description
	 *
	 * @param string $contentDescription
	 */
	public function setContentDescription($contentDescription)
	{
		$this->contentDescription = (string) $contentDescription;
	}

	/**
	 * The return instructions
	 *
	 * @param string $parcelRetrurnInstructions		Allowed values are: RTA, ABANDONED, RTS.
	 */
	public function setParcelReturnInstructions($parcelReturnInstructions)
	{
		$allowedParcelReturnInstructions = array('RTA', 'ABANDONED', 'RTS');

		// validate
		if(!in_array($parcelReturnInstructions, $allowedParcelReturnInstructions))
		{
			throw new bPostException(
				'Invalid value for parcelReturnInstructions (' . $parcelReturnInstructions . '), allowed values are: ' .
				implode(',  ', $allowedParcelReturnInstructions) . '.'
			);
		}
		$this->parcelReturnInstructions = (string) $parcelReturnInstructions;
	}

	/**
	 * The value of the parce in Euro cent
	 *
	 * @param int $parcelValue
	 */
	public function setParcelValue($parcelValue)
	{
		$this->parcelValue = (int) $parcelValue;
	}

	/**
	 * The weight of the parcel in grams
	 *
	 * @param int $parcelWeight
	 */
	public function setParcelWeight($parcelWeight)
	{
		$this->parcelWeight = (int) $parcelWeight;
	}

	/**
	 * Is the address a private address?
	 *
	 * @param bool $privateAddress
	 */
	public function setPrivateAddress($privateAddress)
	{
		$this->privateAddress = (bool) $privateAddress;
	}

	/**
	 * Set the shipment type
	 *
	 * @param string $shipmentType	Allowed values are: SAMPLE, GIFT, OTHER, DOCUMENTS
	 */
	public function setShipmentType($shipmentType)
	{
		$allowedShipmentTypes = array('SAMPLE', 'GIFT', 'OTHER', 'DOCUMENTS');

		// validate
		if(!in_array($shipmentType, $allowedShipmentTypes))
		{
			throw new bPostException(
				'Invalid value for shipmentType (' . $shipmentType . '), allowed values are: ' .
				implode(',  ', $allowedShipmentTypes) . '.'
			);
		}
		$this->shipmentType = (string) $shipmentType;
	}

	/**
	 * Return the object as an array for usage in the XML
	 *
	 * @return array
	 */
	public function toXMLArray()
	{
		$data = array();
		$data['parcelValue'] = $this->parcelValue;
		$data['parcelWeight'] = $this->parcelWeight;
		$data['contentDescription'] = $this->contentDescription;
		$data['shipmentType'] = $this->shipmentType;
		$data['parcelReturnInstructions'] = $this->parcelReturnInstructions;
		$data['privateAddress'] = $this->privateAddress;

		return $data;
	}
}

/**
 * bPost Notification class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class bPostNotification
{
	/**
	 * Generic info
	 *
	 * @var string
	 */
	private $emailAddress, $mobilePhone, $fixedPhone, $language;

	/**
	 * Create a notification
	 *
	 * @param string $language
	 * @param string[otpional] $emailAddress
	 * @param string[otpional] $mobilePhone
	 * @param string[otpional] $fixedPhone
	 */
	public function __construct($language, $emailAddress = null, $mobilePhone = null, $fixedPhone = null)
	{
		if(
			$emailAddress !== null && $mobilePhone !== null ||
			$emailAddress !== null && $fixedPhone !== null ||
			$mobilePhone !== null && $fixedPhone !== null ||
			$fixedPhone !== null && $mobilePhone !== null
		)
		{
			throw new bPostException('You can\'t specify multiple notifications.');
		}

		$this->setLanguage($language);
		if($emailAddress !== null) $this->setEmailAddress($emailAddress);
		if($mobilePhone !== null) $this->setMobilePhone($mobilePhone);
		if($fixedPhone !== null) $this->setFixedPhone($fixedPhone);
	}

	/**
	 * Get the email address
	 *
	 * @return string
	 */
	public function getEmailAddress()
	{
		return $this->emailAddress;
	}

	/**
	 * Get the fixed phone
	 *
	 * @return string
	 */
	public function getFixedPhone()
	{
		return $this->fixedPhone;
	}

	/**
	 * Get the language
	 *
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * Get the mobile phone
	 *
	 * @return string
	 */
	public function getMobilePhone()
	{
		return $this->mobilePhone;
	}

	/**
	 * Set the email address
	 *
	 * @param string $emailAddress
	 */
	public function setEmailAddress($emailAddress)
	{
		if(mb_strlen($emailAddress) > 50) throw new bPostException('Invalid length for emailAddress, maximum is 50.');
		$this->emailAddress = $emailAddress;
	}

	/**
	 * Set the fixed phone
	 *
	 * @param string $fixedPhone
	 */
	public function setFixedPhone($fixedPhone)
	{
		if(mb_strlen($fixedPhone) > 20) throw new bPostException('Invalid length for fixedPhone, maximum is 20.');
		$this->fixedPhone = $fixedPhone;
	}

	/**
	 * Set the language
	 *
	 * @param string $language		Allowed values are EN, NL, FR, DE.
	 */
	public function setLanguage($language)
	{
		$allowedLanguages = array('EN', 'NL', 'FR', 'DE');

		// validate
		if(!in_array($language, $allowedLanguages))
		{
			throw new bPostException(
				'Invalid value for language (' . $language . '), allowed values are: ' .
				implode(',  ', $allowedLanguages) . '.'
			);
		}
		$this->language = $language;
	}

	/**
	 * Set the mobile phone
	 *
	 * @param string $mobilePhone
	 */
	public function setMobilePhone($mobilePhone)
	{
		if(mb_strlen($mobilePhone) > 20) throw new bPostException('Invalid length for mobilePhone, maximum is 20.');
		$this->mobilePhone = $mobilePhone;
	}

	/**
	 * Return the object as an array for usage in the XML
	 *
	 * @return array
	 */
	public function toXMLArray()
	{
		$data = array();
		$data['@attributes']['language'] = $this->language;

		if(isset($this->emailAddress)) $data['emailAddress'] = $this->emailAddress;
		if(isset($this->mobilePhone)) $data['mobilePhone'] = $this->mobilePhone;
		if(isset($this->fixedPhone)) $data['fixedPhone'] = $this->fixedPhone;

		return $data;
	}
}

/**
 * bPost Form handler class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class bPostFormHandler
{
	/**
	 * bPost instance
	 *
	 * @var bPost
	 */
	private $bPost;

	/**
	 * The parameters
	 *
	 * @var array
	 */
	private $parameters = array();


	/**
	 * Create bPostFormHandler instance
	 *
	 * @param string $accountId
	 * @param string $passPhrase
	 */
	public function __construct($accountId, $passPhrase)
	{
		$this->bPost = new bPost($accountId, $passPhrase);
	}

	/**
	 * Calculate the hash
	 *
	 * @return string
	 */
	private function getChecksum()
	{
		// init vars
		$keysToHash = array(
			'accountId', 'action', 'costCenter', 'customerCountry',
			'deliveryMethodOverrides', 'extraSecure', 'orderReference'
		);
		$base = 'accountId=' . $this->bPost->getAccountId() . '&';

		// loop keys
		foreach($keysToHash as $key)
		{
			if(isset($this->parameters[$key]))
			{
				$base .= $key . '=' . $this->parameters[$key] . '&';
			}
		}

		// add passhphrase
		$base .= $this->bPost->getPassPhrase();

		// return the hash
		return hash('sha256', $base);
	}

	/**
	 * Get the parameters
	 *
	 * @param bool $form
	 * @param bool $includeChecksum
	 * @return array
	 */
	public function getParameters($form = false, $includeChecksum = true)
	{
		$return = $this->parameters;

		if($form && isset($return['orderLine']))
		{
			foreach($return['orderLine'] as $key => $value)
			{
				$return['orderLine[' . $key . ']'] = $value;
			}

			unset($return['orderLine']);
		}

		if($includeChecksum)
		{
			$return['accountId'] = $this->bPost->getAccountId();
			$return['checksum'] = $this->getChecksum();
		}

		return $return;
	}

	/**
	 * Set a parameter
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function setParameter($key, $value)
	{
		switch((string) $key)
		{
			// limited values
			case 'action':
			case 'lang':
				$allowedValues['action'] = array('START', 'CONFIRM');
				$allowedValues['lang'] = array('NL', 'FR', 'EN', 'DE', 'Default');

				if(!in_array($value, $allowedValues[$key]))
				{
					throw new bPostException(
						'Invalid value (' . $value . ') for ' . $key . ', allowed values are: ' .
						implode(', ', $allowedValues[$key]) . '.'
					);
				}
				$this->parameters[$key] = $value;
				break;

			// maximum 2 chars
			case 'customerCountry':
				if(mb_strlen($value) > 2)
				{
					throw new bPostException(
						'Invalid length for ' . $key . ', maximum is 2.'
					);
				}
				$this->parameters[$key] = (string) $value;
				break;

			// maximum 8 chars
			case 'customerStreetNumber':
			case 'customerBox':
				if(mb_strlen($value) > 8)
				{
					throw new bPostException(
						'Invalid length for ' . $key . ', maximum is 8.'
					);
				}
				$this->parameters[$key] = (string) $value;
				break;

			// maximum 20 chars
			case 'customerPhoneNumber':
				if(mb_strlen($value) > 20)
				{
					throw new bPostException(
						'Invalid length for ' . $key . ', maximum is 20.'
					);
				}
				$this->parameters[$key] = (string) $value;
				break;

			// maximum 32 chars
			case 'customerPostalCode':
				if(mb_strlen($value) > 32)
				{
					throw new bPostException(
						'Invalid length for ' . $key . ', maximum is 32.'
					);
				}
				$this->parameters[$key] = (string) $value;
				break;

			// maximum 40 chars
			case 'customerFirstName':
			case 'customerLastName':
			case 'customerStreet':
			case 'customerCity':
				if(mb_strlen($value) > 40)
				{
					throw new bPostException(
						'Invalid length for ' . $key . ', maximum is 40.'
					);
				}
				$this->parameters[$key] = (string) $value;
				break;

			// maximum 50 chars
			case 'orderReference':
			case 'costCenter':
			case 'customerEmail':
				if(mb_strlen($value) > 50)
				{
					throw new bPostException(
						'Invalid length for ' . $key . ', maximum is 50.'
					);
				}
				$this->parameters[$key] = (string) $value;
				break;

			// integers
			case 'orderTotalPrice':
			case 'orderWeight':
				$this->parameters[$key] = (int) $value;
				break;

			// array
			case 'orderLine':
				if(!isset($this->parameters[$key])) $this->parameters[$key] = array();
				$this->parameters[$key][] = $value;
				break;

			default:
				$this->parameters[$key] = $value;
		}
	}
}

/**
 * bPost Exception class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class bPostException extends Exception
{}