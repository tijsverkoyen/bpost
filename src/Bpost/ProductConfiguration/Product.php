<?php

namespace TijsVerkoyen\Bpost\Bpost\ProductConfiguration;

use SimpleXMLElement;

class Product
{
    const PRODUCT_NAME_BPACK_EASY_RETOUR = 'bpack Easy Retour';
    const PRODUCT_NAME_BPACK_24H_PRO = 'bpack 24h Pro';
    const PRODUCT_NAME_BPACK_24H_BUSINESS = 'bpack 24h business';
    const PRODUCT_NAME_BPACK_AT_BPOST = 'bpack@bpost';
    const PRODUCT_NAME_BPACK_CLICK_AND_COLLECT = 'bpack Click & Collect';
    const PRODUCT_NAME_BPACK_24_7 = 'bpack 24/7';
    const PRODUCT_NAME_BPACK_WORLD_EASY_RETURN = 'bpack World Easy Return';
    const PRODUCT_NAME_BPACK_WORLD_EXPRESS_PRO = 'bpack World Express Pro';
    const PRODUCT_NAME_BPACK_WORLD_BUSINESS = 'bpack World Business';
    const PRODUCT_NAME_BPACK_EUROPE_BUSINESS = 'bpack Europe Business';

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

    /**
     * @return bool
     */
    public function isForNationalShipping()
    {
        switch ($this->getName()) {
            case self::PRODUCT_NAME_BPACK_EASY_RETOUR:
            case self::PRODUCT_NAME_BPACK_24H_PRO:
            case self::PRODUCT_NAME_BPACK_24H_BUSINESS:
            case self::PRODUCT_NAME_BPACK_AT_BPOST:
            case self::PRODUCT_NAME_BPACK_CLICK_AND_COLLECT:
            case self::PRODUCT_NAME_BPACK_24_7:
                return true;

            case self::PRODUCT_NAME_BPACK_WORLD_EASY_RETURN:
            case self::PRODUCT_NAME_BPACK_WORLD_EXPRESS_PRO:
            case self::PRODUCT_NAME_BPACK_WORLD_BUSINESS:
            case self::PRODUCT_NAME_BPACK_EUROPE_BUSINESS:
            default:
                return false;
        }
    }
}
