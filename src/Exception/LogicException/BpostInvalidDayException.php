<?php

namespace TijsVerkoyen\Bpost\Exception\LogicException;

use TijsVerkoyen\Bpost\Exception\LogicException;

/**
 * Class BpostInvalidDayException
 * @package TijsVerkoyen\Bpost\Exception\LogicException
 */
class BpostInvalidDayException extends BpostInvalidValueException
{
    /**
     * @param string     $invalidValue
     * @param array      $allowedValues
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($invalidValue, array $allowedValues, $code = 0, \Exception $previous = null)
    {
        parent::__construct('day', $invalidValue, $allowedValues, $code, $previous);
    }
}
