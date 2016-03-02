<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box;

use TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Option;

/**
 * bPost National class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
abstract class National implements IBox
{
    /** @var string */
    protected $product;

    /** @var Option[] */
    protected $options;

    /** @var int */
    protected $weight;

    /**
     * @param Option[] $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
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
     * @param string $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return string
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @remark should be implemented by the child class
     * @return array
     */
    public static function getPossibleProductValues()
    {
        return array();
    }

    /**
     * @param int $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @param  \DomDocument $document
     * @param  string       $prefix
     * @param  string       $type
     * @return \DomElement
     */
    public function toXML(\DOMDocument $document, $prefix = null, $type = null)
    {
        $typeElement = $document->createElement($type);

        if ($this->getProduct() !== null) {
            $tagName = 'product';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $typeElement->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getProduct()
                )
            );
        }

        $options = $this->getOptions();
        if (!empty($options)) {
            $optionsElement = $document->createElement('options');
            foreach ($options as $option) {
                $optionsElement->appendChild(
                    $option->toXML($document)
                );
            }
            $typeElement->appendChild($optionsElement);
        }

        if ($this->getWeight() !== null) {
            $typeElement->appendChild(
                $document->createElement($this->getPrefixedTagName($prefix, 'weight'), $this->getWeight())
            );
        }

        return $typeElement;
    }

    /**
     * Prefix $tagName with the $prefix, if needed
     * @param string $prefix
     * @param string $tagName
     * @return string
     */
    protected function getPrefixedTagName($prefix, $tagName)
    {
        if ($prefix !== null) {
            return $prefix . ':' . $tagName;
        }
        return $tagName;
    }

}
