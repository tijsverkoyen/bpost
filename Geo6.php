<?php
namespace TijsVerkoyen\Bpost;

/**
 * Geo6 class
 *
 * This source file can be used to communicate with the Bpost GEO6 webservices
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
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   1.0.1
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class Geo6
{
    // internal constant to enable/disable debugging
    const DEBUG = false;

    // URL for the api
    const API_URL = 'http://taxipost.geo6.be/Locator?';

    // current version
    const VERSION = '3';

    /**
     * @var string
     */
    private $appId;

    /**
     * A cURL instance
     *
     * @var resource
     */
    private $curl;

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
     * Make the real call
     *
     * @param string     $method
     * @param array|null $parameters
     */
    private function doCall($method, $parameters = null)
    {
        // @todo implement me
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
        return (string) 'PHP Geo6/' . self::VERSION . ' ' . $this->userAgent;
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
    public function getNearestServicePoint(
        $street,
        $number,
        $zone,
        $language = 'nl',
        $type = 3,
        $limit = 10
    ) {
        // @todo implement me
    }

    public function getServicePointDetails($id, $language = 'nl', $type = 3)
    {
        // @todo implement me
    }

    public function getServicePointPage($id, $language = 'nl', $type = 3)
    {
        // @todo implement me
    }
}
