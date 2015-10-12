<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box;

use TijsVerkoyen\Bpost\Bpost\Order\Box\Openinghour\Day;
use TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Messaging;
use TijsVerkoyen\Bpost\Bpost\Order\Receiver;
use TijsVerkoyen\Bpost\Exception;

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
    /**
     * @var array
     */
    private $openingHours;

    /**
     * @var string
     */
    private $desiredDeliveryPlace;

    /**
     * @var \TijsVerkoyen\Bpost\Bpost\Order\Receiver
     */
    private $receiver;

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
     * @param \TijsVerkoyen\Bpost\Bpost\Order\Box\Openinghour\Day $day
     */
    public function addOpeningHour(\TijsVerkoyen\Bpost\Bpost\Order\Box\Openinghour\Day $day)
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
     * @param string $product Possible values are:
     *                          * bpack 24h Pro,
     *                          * bpack 24h business
     *                          * bpack Bus
     *                          * bpack Pallet
     *                          * bpack Easy Retour
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

        parent::setProduct($product);
    }

    /**
     * @return array
     */
    public static function getPossibleProductValues()
    {
        return array(
            'bpack 24h Pro',
            'bpack 24h business',
            'bpack Bus',
            'bpack Pallet',
            'bpack Easy Retour',
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
     * @param  string       $type
     * @return \DomElement
     */
    public function toXML(\DOMDocument $document, $prefix = null, $type = null)
    {
        $tagName = 'nationalBox';
        if ($prefix !== null) {
            $tagName = $prefix . ':' . $tagName;
        }
        $nationalElement = $document->createElement($tagName);
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
            $tagName = 'desiredDeliveryPlace';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $boxElement->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getDesiredDeliveryPlace()
                )
            );
        }

        if ($this->getReceiver() !== null) {
            $boxElement->appendChild(
                $this->getReceiver()->toXML($document)
            );
        }

        return $nationalElement;
    }

    /**
     * @param  \SimpleXMLElement $xml
     * @return AtHome
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        $atHome = new AtHome();

        if (isset($xml->atHome->product) && $xml->atHome->product != '') {
            $atHome->setProduct(
                (string) $xml->atHome->product
            );
        }
        if (isset($xml->atHome->options) && !empty($xml->atHome->options)) {
            foreach ($xml->atHome->options as $optionData) {
                $optionData = $optionData->children('http://schema.post.be/shm/deepintegration/v3/common');

                if (in_array($optionData->getName(), array('infoDistributed'))) {
                    $option = Messaging::createFromXML($optionData);
                } else {
                    $className = '\\TijsVerkoyen\\Bpost\\Bpost\\Order\\Box\\Option\\' . ucfirst($optionData->getName());
                    if (!method_exists($className, 'createFromXML')) {
                        throw new Exception('Not Implemented');
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
                (int) $xml->atHome->weight
            );
        }
        if (isset($xml->atHome->openingHours) && $xml->atHome->openingHours != '') {
            throw new Exception('Not Implemented');
            $atHome->setProduct(
                (string) $xml->atHome->openingHours
            );
        }
        if (isset($xml->atHome->desiredDeliveryPlace) && $xml->atHome->desiredDeliveryPlace != '') {
            $atHome->setDesiredDeliveryPlace(
                (string) $xml->atHome->desiredDeliveryPlace
            );
        }
        if (isset($xml->atHome->receiver)) {
            $atHome->setReceiver(
                Receiver::createFromXML(
                    $xml->atHome->receiver->children('http://schema.post.be/shm/deepintegration/v3/common')
                )
            );
        }

        return $atHome;
    }
}
