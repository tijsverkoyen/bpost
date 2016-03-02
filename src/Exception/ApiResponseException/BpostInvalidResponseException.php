<?php

namespace TijsVerkoyen\Bpost\Exception\ApiResponseException;

use TijsVerkoyen\Bpost\Exception\ApiResponseException;

/**
 * Class BpostInvalidResponseException
 * @package TijsVerkoyen\Bpost\Exception\ApiResponseException
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
