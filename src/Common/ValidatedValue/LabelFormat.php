<?php

namespace TijsVerkoyen\Bpost\Common\ValidatedValue;

use TijsVerkoyen\Bpost\Common\ValidatedValue;
use TijsVerkoyen\Bpost\Exception\BpostLogicException\BpostInvalidValueException;

/**
 * Class LabelFormat
 */
class LabelFormat extends ValidatedValue
{
    const FORMAT_A4 = 'A4';
    const FORMAT_A6 = 'A6';

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        parent::setValue(strtoupper($value));
    }

    /**
     * @throws BpostInvalidValueException
     */
    public function validate()
    {
        $this->validateChoice(array(
            self::FORMAT_A4,
            self::FORMAT_A6,
        ));
    }
}
