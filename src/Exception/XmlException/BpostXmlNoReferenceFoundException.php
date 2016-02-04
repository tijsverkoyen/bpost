<?php

namespace TijsVerkoyen\Bpost\Exception\XmlException;

use TijsVerkoyen\Bpost\Exception\BpostXmlException;

class BpostXmlNoReferenceFoundException extends BpostXmlException
{
    /**
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        $message = 'No reference found' . (empty($message) ? '' : ': ' . $message);
        parent::__construct($message, $code, $previous);
    }
}
