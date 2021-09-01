<?php

namespace Bpost\BpostApiClient\ApiCaller;

use Bpost\BpostApiClient\Exception\BpostApiResponseException;
use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostApiBusinessException;
use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostApiSystemException;
use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostCurlException;
use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostInvalidResponseException;
use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostInvalidXmlResponseException;
use Bpost\BpostApiClient\Logger;

/**
 * Class ApiCaller
 * @package Bpost\BpostApiClient\ApiCaller
 * @codeCoverageIgnore That makes a HTTP request with the bpost API
 */
class ApiCaller
{

    /** @var Logger */
    private $logger;

    /** @var int */
    private $responseHttpCode;

    /** @var string */
    private $responseBody;

    /** @var string */
    private $responseContentType;

    /**
     * ApiCaller constructor.
     * @param Logger $logger
     */
    public function __construct(Logger $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @return int
     */
    public function getResponseHttpCode()
    {
        return $this->responseHttpCode;
    }

    /**
     * @return string
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * @return string
     */
    public function getResponseContentType()
    {
        return $this->responseContentType;
    }


    /**
     * @param array $options
     * @return bool
     * @throws BpostApiBusinessException
     * @throws BpostApiResponseException
     * @throws BpostApiSystemException
     * @throws BpostCurlException
     * @throws BpostInvalidResponseException
     * @throws BpostInvalidXmlResponseException
     */
    public function doCall(array $options)
    {
        $curl = curl_init();

        // set options
        curl_setopt_array($curl, $options);

        $this->logger->debug('curl request', $options);

        // execute
        $this->responseBody = curl_exec($curl);
        $errorNumber = curl_errno($curl);
        $errorMessage = curl_error($curl);

        $headers = curl_getinfo($curl);

        $this->logger->debug('curl response', array(
            'status' => $errorNumber . ' (' . $errorMessage . ')',
            'headers' => $headers,
            'response' => $this->responseBody
        ));

        // error?
        if ($errorNumber != 0) {
            throw new BpostCurlException($errorMessage, $errorNumber);
        }

        if (isset($headers['http_code'])) {
            $this->responseHttpCode = $headers['http_code'];
        }

        if (isset($headers['Content-Type'])) {
            $this->responseContentType = $headers['Content-Type'];
        }

        return true;
    }

    /**
     * If the httpCode is 200, return 200
     * If the httpCode is 203, return 200
     * If the httpCode is 404 ,return 404
     * ...
     *
     * @return int
     */
    public function getHttpCodeType()
    {
        return 100 * (int)($this->responseHttpCode / 100);
    }
}
