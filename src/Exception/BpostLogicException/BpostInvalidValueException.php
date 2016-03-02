<?php

namespace TijsVerkoyen\Bpost\Exception\BpostLogicException;

use TijsVerkoyen\Bpost\Exception\BpostLogicException;

/**
 * Class BpostInvalidValueException
 * @package TijsVerkoyen\Bpost\Exception\LogicException
 */
class BpostInvalidValueException extends BpostLogicException
{
    /**
     * @param string     $key
     * @param string     $invalidValue
     * @param array      $allowedValues
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($key, $invalidValue, array $allowedValues, $code = 0, \Exception $previous = null)
    {
        $message = sprintf(
            'Invalid value (%1$s) for %2$s, possible values are: %3$s.',
            $invalidValue,
            $key,
            implode(', ', $allowedValues)
        );
        parent::__construct($message, $code, $previous);
    }
}
