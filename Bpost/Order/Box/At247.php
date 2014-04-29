<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box;

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
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $boxElement->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getParcelsDepotId()
                )
            );
        }
        if ($this->getParcelsDepotName() !== null) {
            $tagName = 'parcelsDepotName';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
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
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $boxElement->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getMemberId()
                )
            );
        }
        if ($this->getReceiverName() !== null) {
            $tagName = 'receiverName';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $boxElement->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getReceiverName()
                )
            );
        }
        if ($this->getReceiverCompany() !== null) {
            $tagName = 'receiverCompany';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $boxElement->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getReceiverCompany()
                )
            );
        }

        return $nationalElement;
    }
}
