<?php

namespace TijsVerkoyen\Bpost\Bpost\ProductConfiguration;

use SimpleXMLElement;

class Product
{
    /** @var  bool */
    private $default;
    /** @var  string */
    private $name;

    /** @var  Price[] */
    private $prices = array();
    /** @var  Option[] */
    private $options = array();

    /**
     * @param SimpleXMLElement $xml
     *
     * @return Product
     */
    public static function createFromXML(SimpleXMLElement $xml)
    {
        /*
        <product default="true" name="bpack 24/7">
          <price price20To30="750" price10To20="650" price5To10="550" price2To5="450" priceLessThan2="350" countryIso2Code="BE"/>
          <option visiblity="NOT_VISIBLE_BY_CONSUMER_OPTIONAL" price="0" name="Saturday"/>
          <option visiblity="NOT_VISIBLE_BY_CONSUMER_OPTIONAL" price="0" name="Info &quot;Distributed&quot;"/>
          <option visiblity="NOT_VISIBLE_BY_CONSUMER_OPTIONAL" price="0" name="Insurance"/>
        </product>
        */
        $attributes = $xml->attributes();
        $children = $xml->children();

        $product = new self();
        $product->setDefault($attributes['default'] == 'true');
        $product->setName($attributes['name']);

        if (isset($children->price)) {
            foreach ($children->price as $priceXml) {
                $product->addPrice(Price::createFromXML($priceXml));
            }
        }
        if (isset($children->option)) {
            foreach ($children->option as $optionXml) {
                $product->addOption(Option::createFromXML($optionXml));
            }
        }
        return $product;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * @param bool $default
     */
    public function setDefault($default)
    {
        $this->default = (bool) $default;
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
     * @return Price[]
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * @param Price $price
     */
    public function addPrice(Price $price)
    {
        $this->prices[] = $price;
    }

    /**
     * @return Option[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param Option $option
     */
    public function addOption(Option $option)
    {
        $this->options[] = $option;
    }

}
