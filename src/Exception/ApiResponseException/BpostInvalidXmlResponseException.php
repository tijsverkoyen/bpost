<?php

namespace Bpost\BpostApiClient\Exception\ApiResponseException;

use Bpost\BpostApiClient\Exception\ApiResponseException;

/**
 * Class BpostInvalidXmlResponseException
 * @package Bpost\BpostApiClient\Exception\ApiResponseException
 */
class BpostInvalidXmlResponseException extends ApiResponseException
{
    /**
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        $message = 'Invalid XML-response' . (empty($message) ? '' : ': ' . $message);
        parent::__construct($message, $code, $previous);
    }
}
