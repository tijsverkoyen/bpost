<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box;

use TijsVerkoyen\Bpost\Exception;

/**
 * bPost International class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class International
{
    /**
     * @var string
     */
    private $product;

    /**
     * @var array
     */
    private $options;

    /**
     * @var \TijsVerkoyen\Bpost\Bpost\Order\Receiver
     */
    private $receiver;

    /**
     * @var int
     */
    private $parcelWeight;

    /**
     * @var \TijsVerkoyen\Bpost\Bpost\Order\Box\CustomsInfo\CustomsInfo
     */
    private $customsInfo;

    /**
     * @param \TijsVerkoyen\Bpost\Bpost\Order\Box\CustomsInfo\CustomsInfo $customsInfo
     */
    public function setCustomsInfo($customsInfo)
    {
        $this->customsInfo = $customsInfo;
    }

    /**
     * @return \TijsVerkoyen\Bpost\Bpost\Order\Box\CustomsInfo\CustomsInfo
     */
    public function getCustomsInfo()
    {
        return $this->customsInfo;
    }

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
     * @param int $parcelWeight
     */
    public function setParcelWeight($parcelWeight)
    {
        $this->parcelWeight = $parcelWeight;
    }

    /**
     * @return int
     */
    public function getParcelWeight()
    {
        return $this->parcelWeight;
    }

    /**
     * @param string $product
     */
    public function setProduct($product)
    {
        if (!in_array($product, self::getPossibleProductValues())) {
            throw new Exception(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', self::getPossibleProductValues())
                )
            );
        }

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
     * @return array
     */
    public static function getPossibleProductValues()
    {
        return array(
            'bpack World Business',
            'bpack World Express Pro',
            'bpack Europe Business',
        );
    }

    /**
     * @param \TijsVerkoyen\Bpost\Bpost\Order\Receiver $receiver
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
    }

    /**
     * @return \TijsVerkoyen\Bpost\Bpost\Order\Receiver
     */
    public function getReceiver()
    {
        return $this->receiver;
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

        if ($this->getReceiver() !== null) {
            $data['receiver'] = $this->getReceiver()->toXMLArray();
        }

        if ($this->getParcelWeight() !== null) {
            $data['parcelWeight'] = $this->getParcelWeight();
        }

        if ($this->getCustomsInfo() !== null) {
            $data['customsInfo'] = $this->getCustomsInfo()->toXMLArray();
        }

        return array('international' => $data);
    }
}
