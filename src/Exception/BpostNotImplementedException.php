<?php

namespace TijsVerkoyen\Bpost\Exception;

use TijsVerkoyen\Bpost\BpostException;

/**
 * Class BpostNotImplementedException
 * @package TijsVerkoyen\Bpost\Exception\LogicException
 */
class BpostNotImplementedException extends BpostException
{
    /**
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        $message = 'Not implemented' . (empty($message) ? '' : ': ' . $message);
        parent::__construct($message, $code, $previous);
    }
}
