<?php

namespace TijsVerkoyen\Bpost;

use TijsVerkoyen\Bpost\Exception\BpostLogicException;
use TijsVerkoyen\Bpost\Exception\BpostLogicException\BpostInvalidLengthException;
use TijsVerkoyen\Bpost\Exception\BpostLogicException\BpostInvalidValueException;

abstract class BasicAttribute
{
    /** @var mixed */
    private $value;

    /** @var string */
    private $key;

    /**
     * BasicAttribute constructor.
     * @param mixed  $value
     * @param string $key
     */
    public function __construct($value, $key = '')
    {
        $this->value = $value;
        $this->setKey($key);
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
     * @param string $key
     */
    private function setKey($key)
    {
        $this->key = (string)($key ?: $this->getDefaultKey());
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
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
            throw new BpostInvalidLengthException($this->getKey(), mb_strlen($this->getValue()), $length);
        }
    }

    /**
     * @param array $allowedValues
     * @throws BpostInvalidValueException
     */
    public function validateChoice(array $allowedValues)
    {
        if (!in_array($this->getValue(), $allowedValues)) {
            throw new BpostInvalidValueException($this->getKey(), $this->getValue(), $allowedValues);
        }
    }

    /**
     * @return string
     */
    protected abstract function getDefaultKey();

    /**
     * @throws BpostLogicException
     */
    public abstract function validate();

}
