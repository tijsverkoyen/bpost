<?php

namespace TijsVerkoyen\Bpost\Exception\BpostLogicException;

use TijsVerkoyen\Bpost\Exception\BpostLogicException;

/**
 * Class BpostInvalidValueException
 * @package TijsVerkoyen\Bpost\Exception\LogicException
 */
class BpostInvalidPatternException extends BpostLogicException
{
    /**
     * @param string     $key
     * @param string     $invalidValue
     * @param string     $regexPattern
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($key, $invalidValue, $regexPattern, $code = 0, \Exception $previous = null)
    {
        $message = sprintf(
            'Invalid value (%1$s) for entry "%2$s", pattern is: "%3$s".',
            $invalidValue,
            $key,
            $regexPattern
        );
        parent::__construct($message, $code, $previous);
    }
}
