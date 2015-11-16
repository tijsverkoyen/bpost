<?php

namespace TijsVerkoyen\Bpost\Bpost\ProductConfiguration;

use SimpleXMLElement;

class Characteristic
{
    /** @var  string */
    private $displayValue;
    /** @var  int */
    private $value;
    /** @var  string */
    private $name;

    /**
     * @param SimpleXMLElement $xml
     *
     * @return Characteristic
     */
    public static function createFromXML(SimpleXMLElement $xml)
    {
        /*
        <characteristic displayValue="Basic (0-500 EUR)" value="1" name="Insurance range code"/>
        */
        $attributes = $xml->attributes();

        $option = new self();
        $option->setDisplayValue($attributes['displayValue']);
        $option->setValue($attributes['value']);
        $option->setName($attributes['name']);

        return $option;
    }

    /**
     * @return string
     */
    public function getDisplayValue()
    {
        return $this->displayValue;
    }

    /**
     * @param string $displayValue
     */
    public function setDisplayValue($displayValue)
    {
        $this->displayValue = (string) $displayValue;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function setValue($value)
    {
        $this->value = (int) $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }

}
