<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box;

use TijsVerkoyen\Bpost\Bpost\Order\ParcelsDepotAddress;
use TijsVerkoyen\Bpost\Exception;

/**
 * bPost At247 class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class At247 extends National
{
    /**
     * @var string
     */
    private $parcelsDepotId;

    /**
     * @var string
     */
    private $parcelsDepotName;

    /**
     * @var \TijsVerkoyen\Bpost\Bpost\Order\ParcelsDepotAddress
     */
    private $parcelsDepotAddress;

    /**
     * @var string
     */
    protected $product = 'bpack 24h Pro';

    /**
     * @var string
     */
    private $memberId;

    /**
     * @var string
     */
    private $receiverName;

    /**
     * @var string
     */
    private $receiverCompany;

    /**
     * @param string $memberId
     */
    public function setMemberId($memberId)
    {
        $this->memberId = $memberId;
    }

    /**
     * @return string
     */
    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * @param \TijsVerkoyen\Bpost\Bpost\Order\ParcelsDepotAddress $parcelsDepotAddress
     */
    public function setParcelsDepotAddress($parcelsDepotAddress)
    {
        $this->parcelsDepotAddress = $parcelsDepotAddress;
    }

    /**
     * @return \TijsVerkoyen\Bpost\Bpost\Order\ParcelsDepotAddress
     */
    public function getParcelsDepotAddress()
    {
        return $this->parcelsDepotAddress;
    }

    /**
     * @param string $parcelsDepotId
     */
    public function setParcelsDepotId($parcelsDepotId)
    {
        $this->parcelsDepotId = $parcelsDepotId;
    }

    /**
     * @return string
     */
    public function getParcelsDepotId()
    {
        return $this->parcelsDepotId;
    }

    /**
     * @param string $parcelsDepotName
     */
    public function setParcelsDepotName($parcelsDepotName)
    {
        $this->parcelsDepotName = $parcelsDepotName;
    }

    /**
     * @return string
     */
    public function getParcelsDepotName()
    {
        return $this->parcelsDepotName;
    }

    /**
     * @param string $product Possible values are: bpack 24h Pro
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
        );
    }

    /**
     * @param string $receiverCompany
     */
    public function setReceiverCompany($receiverCompany)
    {
        $this->receiverCompany = $receiverCompany;
    }

    /**
     * @return string
     */
    public function getReceiverCompany()
    {
        return $this->receiverCompany;
    }

    /**
     * @param string $receiverName
     */
    public function setReceiverName($receiverName)
    {
        $this->receiverName = $receiverName;
    }

    /**
     * @return string
     */
    public function getReceiverName()
    {
        return $this->receiverName;
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
        $boxElement = parent::toXML($document, null, 'at24-7');
        $nationalElement->appendChild($boxElement);

        if ($this->getParcelsDepotId() !== null) {
            $tagName = 'parcelsDepotId';
            $boxElement->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getParcelsDepotId()
                )
            );
        }
        if ($this->getParcelsDepotName() !== null) {
            $tagName = 'parcelsDepotName';
            $boxElement->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getParcelsDepotName()
                )
            );
        }
        if ($this->getParcelsDepotAddress() !== null) {
            $boxElement->appendChild(
                $this->getParcelsDepotAddress()->toXML($document)
            );
        }
        if ($this->getMemberId() !== null) {
            $tagName = 'memberId';
            $boxElement->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getMemberId()
                )
            );
        }
        if ($this->getReceiverName() !== null) {
            $tagName = 'receiverName';
            $boxElement->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getReceiverName()
                )
            );
        }
        if ($this->getReceiverCompany() !== null) {
            $tagName = 'receiverCompany';
            $boxElement->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getReceiverCompany()
                )
            );
        }

        return $nationalElement;
    }

    /**
     * @param  \SimpleXMLElement $xml
     * @return At247
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        $at247 = new At247();

        if (isset($xml->{'at24-7'}->product) && $xml->{'at24-7'}->product != '') {
            $at247->setProduct(
                (string) $xml->{'at24-7'}->product
            );
        }
        if (isset($xml->{'at24-7'}->options)) {
            foreach ($xml->{'at24-7'}->options as $optionData) {
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

                $at247->addOption($option);
            }
        }
        if (isset($xml->{'at24-7'}->weight) && $xml->{'at24-7'}->weight != '') {
            $at247->setWeight(
                (int) $xml->{'at24-7'}->weight
            );
        }
        if (isset($xml->{'at24-7'}->memberId) && $xml->{'at24-7'}->memberId != '') {
            $at247->setMemberId(
                (string) $xml->{'at24-7'}->memberId
            );
        }
        if (isset($xml->{'at24-7'}->receiverName) && $xml->{'at24-7'}->receiverName != '') {
            $at247->setReceiverName(
                (string) $xml->{'at24-7'}->receiverName
            );
        }
        if (isset($xml->{'at24-7'}->receiverCompany) && $xml->{'at24-7'}->receiverCompany != '') {
            $at247->setReceiverCompany(
                (string) $xml->{'at24-7'}->receiverCompany
            );
        }
        if (isset($xml->{'at24-7'}->parcelsDepotId) && $xml->{'at24-7'}->parcelsDepotId != '') {
            $at247->setParcelsDepotId(
                (string) $xml->{'at24-7'}->parcelsDepotId
            );
        }
        if (isset($xml->{'at24-7'}->parcelsDepotName) && $xml->{'at24-7'}->parcelsDepotName != '') {
            $at247->setParcelsDepotName(
                (string) $xml->{'at24-7'}->parcelsDepotName
            );
        }
        if (isset($xml->{'at24-7'}->parcelsDepotAddress)) {
            $parcelsDepotAddressData = $xml->{'at24-7'}->parcelsDepotAddress->children(
                'http://schema.post.be/shm/deepintegration/v3/common'
            );
            $at247->setParcelsDepotAddress(
                ParcelsDepotAddress::createFromXML($parcelsDepotAddressData)
            );
        }

        return $at247;
    }
}
