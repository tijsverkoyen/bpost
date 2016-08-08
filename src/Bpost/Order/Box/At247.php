<?php
namespace Bpost\BpostApiClient\Bpost\Order\Box;

use Bpost\BpostApiClient\Bpost\Order\Box\National\UnregisteredParcelLockerMember;
use Bpost\BpostApiClient\Bpost\Order\Box\Option\Messaging;
use Bpost\BpostApiClient\Bpost\Order\ParcelsDepotAddress;
use Bpost\BpostApiClient\Bpost\ProductConfiguration\Product;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;
use Bpost\BpostApiClient\Exception\BpostNotImplementedException;

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
    /**@var string */
    private $parcelsDepotId;

    /** @var string */
    private $parcelsDepotName;

    /** @var \Bpost\BpostApiClient\Bpost\Order\ParcelsDepotAddress */
    private $parcelsDepotAddress;

    /** @var string */
    protected $product = Product::PRODUCT_NAME_BPACK_24H_PRO;

    /** @var string */
    private $memberId;

    /** @var UnregisteredParcelLockerMember */
    private $unregisteredParcelLockerMember;

    /** @var string */
    private $receiverName;

    /** @var string */
    private $receiverCompany;

    /** @var string */
    protected $requestedDeliveryDate;

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
     * @param \Bpost\BpostApiClient\Bpost\Order\ParcelsDepotAddress $parcelsDepotAddress
     */
    public function setParcelsDepotAddress($parcelsDepotAddress)
    {
        $this->parcelsDepotAddress = $parcelsDepotAddress;
    }

    /**
     * @return \Bpost\BpostApiClient\Bpost\Order\ParcelsDepotAddress
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
     * @return UnregisteredParcelLockerMember
     */
    public function getUnregisteredParcelLockerMember()
    {
        return $this->unregisteredParcelLockerMember;
    }

    /**
     * @param UnregisteredParcelLockerMember $unregisteredParcelLockerMember
     */
    public function setUnregisteredParcelLockerMember(UnregisteredParcelLockerMember $unregisteredParcelLockerMember)
    {
        $this->unregisteredParcelLockerMember = $unregisteredParcelLockerMember;
    }

    /**
     * @param string $product Possible values are: bpack 24h Pro
     *
     * @throws BpostInvalidValueException
     */
    public function setProduct($product)
    {
        if ( ! in_array($product, self::getPossibleProductValues())) {
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
            Product::PRODUCT_NAME_BPACK_24_7,
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
     *
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
            $boxElement->appendChild(
                $document->createElement('parcelsDepotId', $this->getParcelsDepotId())
            );
        }
        if ($this->getParcelsDepotName() !== null) {
            $boxElement->appendChild(
                $document->createElement(
                    'parcelsDepotName',
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
            $boxElement->appendChild(
                $document->createElement(
                    'memberId',
                    $this->getMemberId()
                )
            );
        }
        $this->addToXmlUnregisteredParcelLockerMember($document, $boxElement, $prefix);
        if ($this->getReceiverName() !== null) {
            $boxElement->appendChild(
                $document->createElement(
                    'receiverName',
                    $this->getReceiverName()
                )
            );
        }
        if ($this->getReceiverCompany() !== null) {
            $boxElement->appendChild(
                $document->createElement(
                    'receiverCompany',
                    $this->getReceiverCompany()
                )
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
                    $this->getPrefixedTagName('requestedDeliveryDate', $prefix),
                    $this->getRequestedDeliveryDate()
                )
            );
        }
    }

    /**
     * @param \DOMDocument $document
     * @param \DOMElement  $typeElement
     * @param string       $prefix
     */
    protected function addToXmlUnregisteredParcelLockerMember(\DOMDocument $document, \DOMElement $typeElement, $prefix)
    {
        if ($this->getUnregisteredParcelLockerMember() !== null) {
            $typeElement->appendChild(
                $this->getUnregisteredParcelLockerMember()->toXml($document)
            );
        }
    }

    /**
     * @param  \SimpleXMLElement $xml
     *
     * @return At247
     * @throws BpostInvalidValueException
     * @throws BpostNotImplementedException
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        $at247 = new At247();

        if (isset($xml->{'at24-7'}->product) && $xml->{'at24-7'}->product != '') {
            $at247->setProduct(
                (string)$xml->{'at24-7'}->product
            );
        }
        if (isset($xml->{'at24-7'}->options)) {
            /** @var \SimpleXMLElement $optionData */
            foreach ($xml->{'at24-7'}->options as $optionData) {
                $optionData = $optionData->children('http://schema.post.be/shm/deepintegration/v3/common');

                if (in_array($optionData->getName(), array(Messaging::MESSAGING_TYPE_INFO_DISTRIBUTED))) {
                    $option = Messaging::createFromXML($optionData);
                } else {
                    $className = '\\Bpost\\BpostApiClient\\Bpost\\Order\\Box\\Option\\' . ucfirst($optionData->getName());
                    if ( ! method_exists($className, 'createFromXML')) {
                        throw new BpostNotImplementedException();
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
                (int)$xml->{'at24-7'}->weight
            );
        }
        if (isset($xml->{'at24-7'}->memberId) && $xml->{'at24-7'}->memberId != '') {
            $at247->setMemberId(
                (string)$xml->{'at24-7'}->memberId
            );
        }
        if (isset($xml->{'at24-7'}->receiverName) && $xml->{'at24-7'}->receiverName != '') {
            $at247->setReceiverName(
                (string)$xml->{'at24-7'}->receiverName
            );
        }
        if (isset($xml->{'at24-7'}->receiverCompany) && $xml->{'at24-7'}->receiverCompany != '') {
            $at247->setReceiverCompany(
                (string)$xml->{'at24-7'}->receiverCompany
            );
        }
        if (isset($xml->{'at24-7'}->parcelsDepotId) && $xml->{'at24-7'}->parcelsDepotId != '') {
            $at247->setParcelsDepotId(
                (string)$xml->{'at24-7'}->parcelsDepotId
            );
        }
        if (isset($xml->{'at24-7'}->parcelsDepotName) && $xml->{'at24-7'}->parcelsDepotName != '') {
            $at247->setParcelsDepotName(
                (string)$xml->{'at24-7'}->parcelsDepotName
            );
        }
        if (isset($xml->{'at24-7'}->parcelsDepotAddress)) {
            /** @var \SimpleXMLElement $parcelsDepotAddressData */
            $parcelsDepotAddressData = $xml->{'at24-7'}->parcelsDepotAddress->children(
                'http://schema.post.be/shm/deepintegration/v3/common'
            );
            $at247->setParcelsDepotAddress(
                ParcelsDepotAddress::createFromXML($parcelsDepotAddressData)
            );
        }
        if (isset($xml->{'at24-7'}->requestedDeliveryDate) && $xml->{'at24-7'}->requestedDeliveryDate != '') {
            $at247->setRequestedDeliveryDate(
                (string)$xml->{'at24-7'}->requestedDeliveryDate
            );
        }

        return $at247;
    }
}
