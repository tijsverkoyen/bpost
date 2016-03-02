<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box;

use TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Messaging;
use TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Option;
use TijsVerkoyen\Bpost\Bpost\ProductConfiguration\Product;
use TijsVerkoyen\Bpost\Bpost\Order\Box\Customsinfo\CustomsInfo;
use TijsVerkoyen\Bpost\Bpost\Order\Receiver;
use TijsVerkoyen\Bpost\Exception\LogicException\BpostInvalidValueException;
use TijsVerkoyen\Bpost\Exception\LogicException\BpostNotImplementedException;

/**
 * bPost International class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class International implements IBox
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
     * @throws BpostInvalidValueException
     */
    public function setProduct($product)
    {
        if (!in_array($product, self::getPossibleProductValues())) {
            throw new BpostInvalidValueException('product', $product, self::getPossibleProductValues());
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
            Product::PRODUCT_NAME_BPACK_WORLD_BUSINESS,
            Product::PRODUCT_NAME_BPACK_WORLD_EXPRESS_PRO,
            Product::PRODUCT_NAME_BPACK_EUROPE_BUSINESS,
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
     * @param  \DomDocument $document
     * @param  string       $prefix
     * @return \DomElement
     */
    public function toXML(\DOMDocument $document, $prefix = null)
    {
        $tagName = 'internationalBox';
        if ($prefix !== null) {
            $tagName = $prefix . ':' . $tagName;
        }

        $internationalBox = $document->createElement($tagName);
        $international = $document->createElement('international:international');
        $internationalBox->appendChild($international);

        if ($this->getProduct() !== null) {
            $international->appendChild(
                $document->createElement(
                    'international:product',
                    $this->getProduct()
                )
            );
        }

        $options = $this->getOptions();
        if (!empty($options)) {
            $optionsElement = $document->createElement('international:options');
            foreach ($options as $option) {
                $optionsElement->appendChild(
                    $option->toXML($document)
                );
            }
            $international->appendChild($optionsElement);
        }

        if ($this->getReceiver() !== null) {
            $international->appendChild(
                $this->getReceiver()->toXML($document, 'international')
            );
        }

        if ($this->getParcelWeight() !== null) {
            $international->appendChild(
                $document->createElement(
                    'international:parcelWeight',
                    $this->getParcelWeight()
                )
            );
        }

        if ($this->getCustomsInfo() !== null) {
            $international->appendChild(
                $this->getCustomsInfo()->toXML($document, 'international')
            );
        }

        return $internationalBox;
    }

    /**
     * @param  \SimpleXMLElement $xml
     *
     * @return International
     * @throws BpostInvalidValueException
     * @throws BpostNotImplementedException
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        $international = new International();

        if (isset($xml->international->product) && $xml->international->product != '') {
            $international->setProduct(
                (string) $xml->international->product
            );
        }
        if (isset($xml->international->options)) {
            /** @var \SimpleXMLElement $optionData */
            foreach ($xml->international->options as $optionData) {
                $optionData = $optionData->children('http://schema.post.be/shm/deepintegration/v3/common');

                if (in_array($optionData->getName(), array(Messaging::MESSAGING_TYPE_INFO_DISTRIBUTED))) {
                    $option = Messaging::createFromXML($optionData);
                } else {
                    $className = '\\TijsVerkoyen\\Bpost\\Bpost\\Order\\Box\\Option\\' . ucfirst($optionData->getName());
                    if (!method_exists($className, 'createFromXML')) {
                        throw new BpostNotImplementedException();
                    }
                    $option = call_user_func(
                        array($className, 'createFromXML'),
                        $optionData
                    );
                }

                $international->addOption($option);
            }
        }
        if (isset($xml->international->parcelWeight) && $xml->international->parcelWeight != '') {
            $international->setParcelWeight(
                (int) $xml->international->parcelWeight
            );
        }
        if (isset($xml->international->receiver)) {
            $receiverData = $xml->international->receiver->children(
                'http://schema.post.be/shm/deepintegration/v3/common'
            );
            $international->setReceiver(
                Receiver::createFromXML($receiverData)
            );
        }
        if (isset($xml->international->customsInfo)) {
            $international->setCustomsInfo(
                CustomsInfo::createFromXML($xml->international->customsInfo)
            );
        }

        return $international;
    }
}
