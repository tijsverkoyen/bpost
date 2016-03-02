<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box;

use TijsVerkoyen\Bpost\Bpost\Order\Box\Openinghour\Day;
use TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Messaging;
use TijsVerkoyen\Bpost\Bpost\Order\Receiver;
use TijsVerkoyen\Bpost\Bpost\ProductConfiguration\Product;
use TijsVerkoyen\Bpost\Exception\LogicException\BpostInvalidValueException;
use TijsVerkoyen\Bpost\Exception\LogicException\BpostNotImplementedException;

/**
 * bPost AtHome class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class AtHome extends National
{
    /** @var array */
    private $openingHours;

    /** @var string */
    private $desiredDeliveryPlace;

    /** @var \TijsVerkoyen\Bpost\Bpost\Order\Receiver */
    private $receiver;

    /** @var string */
    protected $requestedDeliveryDate;

    /**
     * @param string $desiredDeliveryPlace
     */
    public function setDesiredDeliveryPlace($desiredDeliveryPlace)
    {
        $this->desiredDeliveryPlace = $desiredDeliveryPlace;
    }

    /**
     * @return string
     */
    public function getDesiredDeliveryPlace()
    {
        return $this->desiredDeliveryPlace;
    }

    /**
     * @param array $openingHours
     */
    public function setOpeningHours($openingHours)
    {
        $this->openingHours = $openingHours;
    }

    /**
     * @param Day $day
     */
    public function addOpeningHour(Day $day)
    {
        $this->openingHours[] = $day;
    }

    /**
     * @return array
     */
    public function getOpeningHours()
    {
        return $this->openingHours;
    }

    /**
     * @param string $product
     * @see getPossibleProductValues
     * @throws BpostInvalidValueException
     */
    public function setProduct($product)
    {
        if (!in_array($product, self::getPossibleProductValues())) {
            throw new BpostInvalidValueException('product', $product, self::getPossibleProductValues());
        }

        parent::setProduct($product);
    }

    /**
     * @return array
     */
    public static function getPossibleProductValues()
    {
        return array(
            Product::PRODUCT_NAME_BPACK_24H_PRO,
            Product::PRODUCT_NAME_BPACK_24H_BUSINESS,
            Product::PRODUCT_NAME_BPACK_BUSINESS,
            Product::PRODUCT_NAME_BPACK_PALLET,
            Product::PRODUCT_NAME_BPACK_EASY_RETOUR,
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
     * @return string
     */
    public function getRequestedDeliveryDate()
    {
        return $this->requestedDeliveryDate;
    }

    /**
     * @param string $requestedDeliveryDate
     */
    public function setRequestedDeliveryDate($requestedDeliveryDate)
    {
        $this->requestedDeliveryDate = (string)$requestedDeliveryDate;
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
        $nationalElement = $document->createElement($this->getPrefixedTagName($prefix, 'nationalBox'));
        $boxElement = parent::toXML($document, null, 'atHome');
        $nationalElement->appendChild($boxElement);

        $openingHours = $this->getOpeningHours();
        if (!empty($openingHours)) {
            $openingHoursElement = $document->createElement('openingHours');
            foreach ($openingHours as $day) {
                /** @var $day \TijsVerkoyen\Bpost\Bpost\Order\Box\Openinghour\Day */
                $openingHoursElement->appendChild(
                    $day->toXML($document)
                );
            }
            $boxElement->appendChild($openingHoursElement);
        }

        if ($this->getDesiredDeliveryPlace() !== null) {
            $boxElement->appendChild(
                $document->createElement(
                    $this->getPrefixedTagName($prefix, 'desiredDeliveryPlace'),
                    $this->getDesiredDeliveryPlace()
                )
            );
        }

        if ($this->getReceiver() !== null) {
            $boxElement->appendChild(
                $this->getReceiver()->toXML($document)
            );
        }

        $this->addToXmlRequestedDeliveryDate($document, $boxElement, $prefix);

        return $nationalElement;
    }

    /**
     * @param \DOMDocument $document
     * @param \DOMElement  $typeElement
     * @param string       $prefix
     */
    protected function addToXmlRequestedDeliveryDate(\DOMDocument $document, \DOMElement $typeElement, $prefix)
    {
        if ($this->getRequestedDeliveryDate() !== null) {
            $typeElement->appendChild(
                $document->createElement(
                    $this->getPrefixedTagName($prefix, 'requestedDeliveryDate'),
                    $this->getRequestedDeliveryDate()
                )
            );
        }
    }

    /**
     * @param  \SimpleXMLElement $xml
     *
     * @return AtHome
     * @throws BpostInvalidValueException
     * @throws BpostNotImplementedException
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        $atHome = new AtHome();

        if (isset($xml->atHome->product) && $xml->atHome->product != '') {
            $atHome->setProduct(
                (string)$xml->atHome->product
            );
        }
        if (isset($xml->atHome->options) && !empty($xml->atHome->options)) {
            /** @var \SimpleXMLElement $optionData */
            foreach ($xml->atHome->options as $optionData) {
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

                $atHome->addOption($option);
            }
        }
        if (isset($xml->atHome->weight) && $xml->atHome->weight != '') {
            $atHome->setWeight(
                (int)$xml->atHome->weight
            );
        }
        if (isset($xml->atHome->openingHours) && $xml->atHome->openingHours != '') {
            throw new BpostNotImplementedException();
            $atHome->setProduct(
                (string)$xml->atHome->openingHours
            );
        }
        if (isset($xml->atHome->desiredDeliveryPlace) && $xml->atHome->desiredDeliveryPlace != '') {
            $atHome->setDesiredDeliveryPlace(
                (string)$xml->atHome->desiredDeliveryPlace
            );
        }
        if (isset($xml->atHome->receiver)) {
            $atHome->setReceiver(
                Receiver::createFromXML(
                    $xml->atHome->receiver->children('http://schema.post.be/shm/deepintegration/v3/common')
                )
            );
        }
        if (isset($xml->atHome->requestedDeliveryDate) && $xml->atHome->requestedDeliveryDate != '') {
            $atHome->setRequestedDeliveryDate(
                $xml->atHome->requestedDeliveryDate
            );
        }

        return $atHome;
    }
}
