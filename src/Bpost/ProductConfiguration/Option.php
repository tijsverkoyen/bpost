<?php

namespace TijsVerkoyen\Bpost\Bpost\ProductConfiguration;

use SimpleXMLElement;

class Option
{

    const OPTION_VISIBILITY_NOT_VISIBLE_BY_CONSUMER_OPTIONAL = 'NOT_VISIBLE_BY_CONSUMER_OPTIONAL';
    const OPTION_VISIBILITY_NOT_VISIBLE_BY_CONSUMER_DEFAULT = 'NOT_VISIBLE_BY_CONSUMER_DEFAULT';
    const OPTION_VISIBILITY_VISIBLE_BY_CONSUMER_AND_MANDATORY = 'VISIBLE_BY_CONSUMER_AND_MANDATORY';

    /** @var  string */
    private $visibility;
    /** @var  int */
    private $price;
    /** @var  string */
    private $name;
    /** @var Characteristic[] */
    private $characteristics = array();

    /**
     * @param SimpleXMLElement $xml
     *
     * @return Option
     */
    public static function createFromXML(SimpleXMLElement $xml)
    {
        /*
        <option visiblity="NOT_VISIBLE_BY_CONSUMER_OPTIONAL" price="0" name="Cash on delivery"/>
        */
        $attributes = $xml->attributes();
        $children = $xml->children();

        $option = new self();
        $option->setVisibility($attributes['visiblity']);
        $option->setPrice($attributes['price']);
        $option->setName($attributes['name']);

        if (isset($children->chracteristic)) {
            foreach ($children->chracteristic as $characteristicXml) {
                $option->addCharacteristic(Characteristic::createFromXML($characteristicXml));
            }
        }

        return $option;
    }

    /**
     * @return string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param string $visibility
     */
    public function setVisibility($visibility)
    {
        $this->visibility = (string) $visibility;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice($price)
    {
        $this->price = (int) $price;
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

    /**
     * @return Characteristic[]
     */
    public function getCharacteristics()
    {
        return $this->characteristics;
    }

    /**
     * @param Characteristic $characteristic
     */
    public function addCharacteristic(Characteristic $characteristic)
    {
        $this->characteristics[] = $characteristic;
    }

}
