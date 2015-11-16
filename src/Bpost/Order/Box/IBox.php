<?php

namespace TijsVerkoyen\Bpost\Bpost\Order\Box;

interface IBox
{

    /**
     * @param array $options
     */
    public function setOptions($options);

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param \TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Option $option
     */
    public function addOption(Option\Option $option);

    /**
     * @param string $product
     */
    public function setProduct($product);

    /**
     * @return string
     */
    public function getProduct();

    /**
     * @remark should be implemented by the child class
     * @return array
     */
    public static function getPossibleProductValues();

    /**
     * Return the object as an array for usage in the XML
     * @param  \DomDocument $document
     * @param  string $prefix
     * @return \DomElement
     */
    public function toXML(\DOMDocument $document, $prefix = null);

    /**
     * @param  \SimpleXMLElement $xml
     * @return self
     */
    public static function createFromXML(\SimpleXMLElement $xml);
}
