<?php
namespace Bpost\BpostApiClient\Bpost\Order\Box;

use Bpost\BpostApiClient\Bpost\Order\Receiver;
use Bpost\BpostApiClient\Bpost\ProductConfiguration\Product;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;
use Bpost\BpostApiClient\Exception\BpostNotImplementedException;
use Bpost\BpostApiClient\Exception\XmlException\BpostXmlInvalidItemException;

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
    /** @var \Bpost\BpostApiClient\Bpost\Order\Receiver */
    private $receiver;

    /** @var string */
    protected $requestedDeliveryDate;

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
     * @param \Bpost\BpostApiClient\Bpost\Order\Receiver $receiver
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
    }

    /**
     * @return \Bpost\BpostApiClient\Bpost\Order\Receiver
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
        $nationalElement = $document->createElement($this->getPrefixedTagName('nationalBox', $prefix));
        $boxElement = parent::toXML($document, null, 'atHome');
        $nationalElement->appendChild($boxElement);

        if ($this->getReceiver() !== null) {
            $boxElement->appendChild(
                $this->getReceiver()->toXML($document)
            );
        }

        $this->addToXmlRequestedDeliveryDate($document, $boxElement);

        return $nationalElement;
    }

    /**
     * @param \DOMDocument $document
     * @param \DOMElement  $typeElement
     */
    protected function addToXmlRequestedDeliveryDate(\DOMDocument $document, \DOMElement $typeElement)
    {
        if ($this->getRequestedDeliveryDate() !== null) {
            $typeElement->appendChild(
                $document->createElement(
                    'requestedDeliveryDate',
                    $this->getRequestedDeliveryDate()
                )
            );
        }
    }

    /**
     * @param  \SimpleXMLElement $xml
     * @return AtHome
     * @throws BpostNotImplementedException
     * @throws BpostXmlInvalidItemException
     * @throws \Bpost\BpostApiClient\BpostException
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        $self = new self();

        if (!isset($xml->atHome)) {
            throw new BpostXmlInvalidItemException();
        }

        $atHomeXml = $xml->atHome[0];

        $self = parent::createFromXML($atHomeXml, $self);

        if (isset($atHomeXml->receiver)) {
            $self->setReceiver(
                Receiver::createFromXML(
                    $atHomeXml->receiver->children('http://schema.post.be/shm/deepintegration/v3/common')
                )
            );
        }

        if (isset($atHomeXml->requestedDeliveryDate) && $atHomeXml->requestedDeliveryDate != '') {
            $self->setRequestedDeliveryDate(
                $atHomeXml->requestedDeliveryDate
            );
        }

        return $self;
    }
}
