<?php
namespace Bpost\BpostApiClient;

use Bpost\BpostApiClient\Exception\ApiResponseException\BpostCurlException;
use Bpost\BpostApiClient\Exception\ApiResponseException\BpostInvalidXmlResponseException;
use Bpost\BpostApiClient\Exception\ApiResponseException\BpostTaxipostLocatorException;
use Bpost\BpostApiClient\Geo6\Poi;

/**
 * Geo6 class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class Geo6
{
    // URL for the api
    const API_URL = 'https://taxipost.geo6.be/Locator';

    // current version
    const VERSION = '3';

    /**
     * @var string
     */
    private $appId;

    /**
     * @var string
     */
    private $partner;

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
     * Constructor
     * @param string $partner Static parameter used for protection/statistics
     * @param string $appId   Static parameter used for protection/statistics
     */
    public function __construct($partner, $appId)
    {
        $this->setPartner((string) $partner);
        $this->setAppId((string) $appId);
    }

    /**
     * Build the url to be called
     *
     * @param string $method
     * @param array  $parameters
     * @return string
     */
    private function buildUrl($method, array $parameters = array())
    {
        return self::API_URL . '?' . $this->buildParameters($method, $parameters);
    }

    /**
     * Build the parameters to send (URL-encoded string)
     *
     * @param  string $method
     * @param  array  $parameters
     * @return string
     */
    private function buildParameters($method, array $parameters = array())
    {
        // add credentials
        $parameters['Function'] = $method;
        $parameters['Partner'] = $this->getPartner();
        $parameters['AppId'] = $this->getAppId();
        $parameters['Format'] = 'xml';

        return http_build_query($parameters);
    }

    /**
     * Make the real call
     *
     * @param  string $method
     * @param  array  $parameters
     * @return \SimpleXMLElement
     * @throws BpostCurlException
     * @throws BpostInvalidXmlResponseException
     * @throws BpostTaxipostLocatorException
     */
    private function doCall($method, array $parameters = array())
    {
        $options[CURLOPT_URL] = $this->buildUrl($method, $parameters);
        $options[CURLOPT_USERAGENT] = $this->getUserAgent();
        $options[CURLOPT_FOLLOWLOCATION] = true;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        $options[CURLOPT_SSL_VERIFYHOST] = false;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_TIMEOUT] = (int) $this->getTimeOut();

        $curl = curl_init();

        // set options
        curl_setopt_array($curl, $options);

        // execute
        $response = curl_exec($curl);
        $errorNumber = curl_errno($curl);
        $errorMessage = curl_error($curl);

        // error?
        if ($errorNumber != '') {
            throw new BpostCurlException($errorMessage, $errorNumber);
        }

        // we expect XML so decode it
        $xml = @simplexml_load_string($response);

        // validate xml
        if ($xml === false || (isset($xml->head) && isset($xml->body))) {
            throw new BpostInvalidXmlResponseException();
        }

        // catch generic errors
        if (isset($xml['type']) && (string) $xml['type'] == 'TaxipostLocatorError') {
            throw new BpostTaxipostLocatorException((string) $xml->txt, (int)$xml->status);
        }

        // return
        return $xml;
    }

    /**
     * @param string $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param string $partner
     */
    public function setPartner($partner)
    {
        $this->partner = $partner;
    }

    /**
     * @return string
     */
    public function getPartner()
    {
        return $this->partner;
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
        return (string) 'PHP Bpost Geo6/' . self::VERSION . ' ' . $this->userAgent;
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
    /**
     * The GetNearestServicePoints web service delivers the nearest bpost pick-up points to a location
     *
     * @param string $street   Street name
     * @param string $number   Street number
     * @param string $zone     Postal code and/or city
     * @param string $language Language, possible values are: nl, fr
     * @param int    $type     Requested point type, possible values are:
     *                         - 1: Post Office
     *                         - 2: Post Point
     *                         - 3: (1+2, Post Office + Post Point)
     *                         - 4: bpack 24/7
     *                         - 7: (1+2+4, Post Office + Post Point + bpack 24/7)
     * @param  int   $limit
     * @return array
     * @throws BpostCurlException
     * @throws BpostInvalidXmlResponseException
     * @throws BpostTaxipostLocatorException
     */
    public function getNearestServicePoint($street, $number, $zone, $language = 'nl', $type = 3, $limit = 10)
    {
        $parameters = array();
        $parameters['Street'] = (string) $street;
        $parameters['Number'] = (string) $number;
        $parameters['Zone'] = (string) $zone;
        $parameters['Language'] = (string) $language;
        $parameters['Type'] = (int) $type;
        $parameters['Limit'] = (int) $limit;

        $xml = $this->doCall('search', $parameters);

        if (!isset($xml->PoiList->Poi)) {
            throw new BpostInvalidXmlResponseException();
        }

        $pois = array();
        foreach ($xml->PoiList->Poi as $poi) {
            $pois[] = array(
                'poi' => Poi::createFromXML($poi->Record),
                'distance' => (float) $poi->Distance,
            );
        }

        return $pois;
    }

    /**
     * The GetServicePointDetails web service delivers the details for a bpost
     * pick up point referred to by its identifier.
     *
     * @param string $id       Requested point identifier
     * @param string $language Language, possible values: nl, fr
     * @param int    $type     Requested point type, possible values are:
     *                         - 1: Post Office
     *                         - 2: Post Point
     *                         - 4: bpack 24/7
     *
     * @return Poi
     * @throws BpostCurlException
     * @throws BpostInvalidXmlResponseException
     * @throws BpostTaxipostLocatorException
     */
    public function getServicePointDetails($id, $language = 'nl', $type = 3)
    {
        $parameters = array();
        $parameters['Id'] = (string) $id;
        $parameters['Language'] = (string) $language;
        $parameters['Type'] = (int) $type;

        $xml = $this->doCall('info', $parameters);

        if (!isset($xml->Poi->Record)) {
            throw new BpostInvalidXmlResponseException();
        }

        return Poi::createFromXML($xml->Poi->Record);
    }

    /**
     * @param         $id
     * @param  string $language
     * @param  int    $type
     * @return string
     */
    public function getServicePointPage($id, $language = 'nl', $type = 3)
    {
        $parameters = array();
        $parameters['Id'] = (string) $id;
        $parameters['Language'] = (string) $language;
        $parameters['Type'] = (int) $type;

        return $this->buildUrl('page', $parameters);
    }
}
