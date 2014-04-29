<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box;

use TijsVerkoyen\Bpost\Exception;

/**
 * bPost AtBpost class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class AtBpost extends National
{
    /**
     * @var string
     */
    protected $product = 'bpack@bpost';

    /**
     * @var string
     */
    private $pugoId;

    /**
     * @var string
     */
    private $pugoName;

    /**
     * @var \TijsVerkoyen\Bpost\Bpost\Order\PugoAddress;
     */
    private $pugoAddress;

    /**
     * @var string
     */
    private $receiverName;

    /**
     * @var string
     */
    private $receiverCompany;

    /**
     * @param string $product Possible values are: bpack@bpost
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
            'bpack@bpost',
        );
    }

    /**
     * @param \TijsVerkoyen\Bpost\Bpost\Order\PugoAddress $pugoAddress
     */
    public function setPugoAddress($pugoAddress)
    {
        $this->pugoAddress = $pugoAddress;
    }

    /**
     * @return \TijsVerkoyen\Bpost\Bpost\Order\PugoAddress
     */
    public function getPugoAddress()
    {
        return $this->pugoAddress;
    }

    /**
     * @param string $pugoId
     */
    public function setPugoId($pugoId)
    {
        $this->pugoId = $pugoId;
    }

    /**
     * @return string
     */
    public function getPugoId()
    {
        return $this->pugoId;
    }

    /**
     * @param string $pugoName
     */
    public function setPugoName($pugoName)
    {
        $this->pugoName = $pugoName;
    }

    /**
     * @return string
     */
    public function getPugoName()
    {
        return $this->pugoName;
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
     * @param \DomDocument $document
     * @param string       $prefix
     * @param string       $type
     * @return \DomElement
     */
    public function toXML(\DOMDocument $document, $prefix = null, $type = null)
    {
        $tagName = 'nationalBox';
        if ($prefix !== null) {
            $tagName = $prefix . ':' . $tagName;
        }
        $nationalElement = $document->createElement($tagName);
        $boxElement = parent::toXML($document, null, 'atBpost');
        $nationalElement->appendChild($boxElement);

        if ($this->getPugoId() !== null) {
            $tagName = 'pugoId';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $boxElement->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getPugoId()
                )
            );
        }
        if ($this->getPugoName() !== null) {
            $tagName = 'pugoName';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $boxElement->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getPugoName()
                )
            );
        }
        if ($this->getPugoAddress() !== null) {
            $boxElement->appendChild(
                $this->getPugoAddress()->toXML($document, null)
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
