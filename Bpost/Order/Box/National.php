<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box;

/**
 * bPost National class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
abstract class National
{
    /**
     * @var string
     */
    protected $product;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var int
     */
    protected $weight;

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param \TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Option $option
     */
    public function addOption(Option\Option $option)
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
     * @return array
     */
    public function toXMLArray()
    {
        $data = array();
        if ($this->getProduct() !== null) {
            $data['product'] = $this->getProduct();
        }

        $options = $this->getOptions();
        if (!empty($options)) {
            foreach ($options as $option) {
                $data['options'][] = $option->toXMLArray();
            }
        }

        if ($this->getWeight() !== null) {
            $data['weight'] = $this->getWeight();
        }

        return $data;
    }
}
