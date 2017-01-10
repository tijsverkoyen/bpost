<?php

namespace Bpost\BpostApiClient\Common;

use Bpost\BpostApiClient\Exception\BpostLogicException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidPatternException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;

/**
 * Class ValidatedValue
 */
abstract class ValidatedValue
{
    /** @var mixed */
    private $value;

    /**
     * ValidatedValue constructor.
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->setValue($value);
        $this->validate();
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getValue();
    }

    /**
     * @param int $length
     * @throws BpostInvalidLengthException
     */
    public function validateLength($length)
    {
        if (mb_strlen($this->getValue()) > $length) {
            throw new BpostInvalidLengthException('', mb_strlen($this->getValue()), $length);
        }
    }

    /**
     * @param array $allowedValues
     * @throws BpostInvalidValueException
     */
    public function validateChoice(array $allowedValues)
    {
        if (!in_array($this->getValue(), $allowedValues)) {
            throw new BpostInvalidValueException('', $this->getValue(), $allowedValues);
        }
    }

    /**
     * @param string $regexPattern
     * @throws BpostInvalidPatternException
     */
    public function validatePattern($regexPattern)
    {
        if (!preg_match("/^$regexPattern\$/", $this->getValue())) {
            throw new BpostInvalidPatternException('', $this->getValue(), $regexPattern);
        }
    }

    /**
     * @throws BpostLogicException
     */
    public abstract function validate();

}
