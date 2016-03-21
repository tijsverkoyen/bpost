<?php

namespace Bpost\BpostApiClient\Exception\ApiResponseException;

use Bpost\BpostApiClient\Exception\ApiResponseException;

/**
 * Class BpostInvalidResponseException
 * @package Bpost\BpostApiClient\Exception\ApiResponseException
 */
class BpostInvalidResponseException extends ApiResponseException
{
    /**
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        $message = 'Invalid response' . (empty($message) ? '' : ': ' . $message);
        parent::__construct($message, $code, $previous);
    }
}
