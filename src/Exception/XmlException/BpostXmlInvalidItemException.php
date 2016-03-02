<?php

namespace TijsVerkoyen\Bpost\Exception\XmlException;

use TijsVerkoyen\Bpost\Exception\BpostXmlException;

/**
 * Class BpostXmlInvalidItemException
 * @package TijsVerkoyen\Bpost\Exception\XmlException
 */
class BpostXmlInvalidItemException extends BpostXmlException
{
    /**
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        $message = 'Invalid item' . (empty($message) ? '' : ': ' . $message);
        parent::__construct($message, $code, $previous);
    }
}
