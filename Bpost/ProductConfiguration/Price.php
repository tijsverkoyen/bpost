<?php

namespace TijsVerkoyen\Bpost\Bpost\ProductConfiguration;

use SimpleXMLElement;

class Price
{
    /** @var  string */
    private $countryIso2;

    /** @var  int */
    private $priceLessThan2;
    /** @var  int */
    private $price2To5;
    /** @var  int */
    private $price5To10;
    /** @var  int */
    private $price10To20;
    /** @var  int */
    private $price20To30;

    /**
     * @param SimpleXMLElement $xml
     *
     * @return Price
     */
    public static function createFromXML(SimpleXMLElement $xml)
    {
        /*
        <price price20To30="820" price10To20="720" price5To10="620" price2To5="520" priceLessThan2="420" countryIso2Code="BE"/>
        */
        $attributes = $xml->attributes();

        $price = new self();
        $price->setCountryIso2($attributes['countryIso2Code']);
        $price->setPriceLessThan2($attributes['priceLessThan2']);
        $price->setPrice2To5($attributes['price2To5']);
        $price->setPrice5To10($attributes['price5To10']);
        $price->setPrice10To20($attributes['price10To20']);
        $price->setPrice20To30($attributes['price20To30']);

        return $price;
    }

    /**
     * @return string
     */
    public function getCountryIso2()
    {
        return $this->countryIso2;
    }

    /**
     * @param string $countryIso2
     */
    public function setCountryIso2($countryIso2)
    {
        $this->countryIso2 = (string) $countryIso2;
    }

    /**
     * @return int
     */
    public function getPriceLessThan2()
    {
        return $this->priceLessThan2;
    }

    /**
     * @param int $priceLessThan2
     */
    public function setPriceLessThan2($priceLessThan2)
    {
        $this->priceLessThan2 = (int) $priceLessThan2;
    }

    /**
     * @return int
     */
    public function getPrice2To5()
    {
        return $this->price2To5;
    }

    /**
     * @param int $price2To5
     */
    public function setPrice2To5($price2To5)
    {
        $this->price2To5 = (int) $price2To5;
    }

    /**
     * @return int
     */
    public function getPrice5To10()
    {
        return $this->price5To10;
    }

    /**
     * @param int $price5To10
     */
    public function setPrice5To10($price5To10)
    {
        $this->price5To10 = (int) $price5To10;
    }

    /**
     * @return int
     */
    public function getPrice10To20()
    {
        return $this->price10To20;
    }

    /**
     * @param int $price10To20
     */
    public function setPrice10To20($price10To20)
    {
        $this->price10To20 = (int) $price10To20;
    }

    /**
     * @return int
     */
    public function getPrice20To30()
    {
        return $this->price20To30;
    }

    /**
     * @param int $price20To30
     */
    public function setPrice20To30($price20To30)
    {
        $this->price20To30 = (int) $price20To30;
    }
}
