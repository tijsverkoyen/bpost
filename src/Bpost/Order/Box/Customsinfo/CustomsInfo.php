<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box\Customsinfo;

use TijsVerkoyen\Bpost\Exception\BpostLogicException\BpostInvalidLengthException;
use TijsVerkoyen\Bpost\Exception\BpostLogicException\BpostInvalidValueException;

/**
 * bPost CustomsInfo class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class CustomsInfo
{

    const CUSTOM_INFO_PARCEL_RETURN_INSTRUCTION_RTA = 'RTA';
    const CUSTOM_INFO_PARCEL_RETURN_INSTRUCTION_RTS = 'RTS';
    const CUSTOM_INFO_PARCEL_RETURN_INSTRUCTION_ABANDONED = 'ABANDONED';

    const CUSTOM_INFO_SHIPMENT_TYPE_SAMPLE = 'SAMPLE';
    const CUSTOM_INFO_SHIPMENT_TYPE_GIFT = 'GIFT';
    const CUSTOM_INFO_SHIPMENT_TYPE_GOODS = 'GOODS';
    const CUSTOM_INFO_SHIPMENT_TYPE_DOCUMENTS = 'DOCUMENTS';
    const CUSTOM_INFO_SHIPMENT_TYPE_OTHER = 'OTHER';

    /**
     * @var int
     */
    private $parcelValue;

    /**
     * @var string
     */
    private $contentDescription;

    /**
     * @var string
     */
    private $shipmentType;

    /**
     * @var string
     */
    private $parcelReturnInstructions;

    /**
     * @var bool
     */
    private $privateAddress;

    /**
     * @param string $contentDescription
     * @throws BpostInvalidLengthException
     */
    public function setContentDescription($contentDescription)
    {
        $length = 50;
        if (mb_strlen($contentDescription) > $length) {
            throw new BpostInvalidLengthException('contentDescription', mb_strlen($contentDescription), $length);
        }

        $this->contentDescription = $contentDescription;
    }

    /**
     * @return string
     */
    public function getContentDescription()
    {
        return $this->contentDescription;
    }

    /**
     * @param string $parcelReturnInstructions
     * @throws BpostInvalidValueException
     */
    public function setParcelReturnInstructions($parcelReturnInstructions)
    {
        $parcelReturnInstructions = strtoupper($parcelReturnInstructions);

        if (!in_array($parcelReturnInstructions, self::getPossibleParcelReturnInstructionValues())) {
            throw new BpostInvalidValueException(
                'parcelReturnInstructions',
                $parcelReturnInstructions,
                self::getPossibleParcelReturnInstructionValues()
            );
        }

        $this->parcelReturnInstructions = $parcelReturnInstructions;
    }

    /**
     * @return string
     */
    public function getParcelReturnInstructions()
    {
        return $this->parcelReturnInstructions;
    }

    /**
     * @return array
     */
    public static function getPossibleParcelReturnInstructionValues()
    {
        return array(
            self::CUSTOM_INFO_PARCEL_RETURN_INSTRUCTION_RTA,
            self::CUSTOM_INFO_PARCEL_RETURN_INSTRUCTION_RTS,
            self::CUSTOM_INFO_PARCEL_RETURN_INSTRUCTION_ABANDONED,
        );
    }

    /**
     * @param int $parcelValue
     */
    public function setParcelValue($parcelValue)
    {
        $this->parcelValue = $parcelValue;
    }

    /**
     * @return int
     */
    public function getParcelValue()
    {
        return $this->parcelValue;
    }

    /**
     * @param boolean $privateAddress
     */
    public function setPrivateAddress($privateAddress)
    {
        $this->privateAddress = $privateAddress;
    }

    /**
     * @return boolean
     */
    public function getPrivateAddress()
    {
        return $this->privateAddress;
    }

    /**
     * @param string $shipmentType
     * @throws BpostInvalidValueException
     */
    public function setShipmentType($shipmentType)
    {
        $shipmentType = strtoupper($shipmentType);

        if (!in_array($shipmentType, self::getPossibleShipmentTypeValues())) {
            throw new BpostInvalidValueException('shipmentType', $shipmentType, self::getPossibleShipmentTypeValues());
        }

        $this->shipmentType = $shipmentType;
    }

    /**
     * @return string
     */
    public function getShipmentType()
    {
        return $this->shipmentType;
    }

    /**
     * @return array
     */
    public static function getPossibleShipmentTypeValues()
    {
        return array(
            self::CUSTOM_INFO_SHIPMENT_TYPE_SAMPLE,
            self::CUSTOM_INFO_SHIPMENT_TYPE_GIFT,
            self::CUSTOM_INFO_SHIPMENT_TYPE_GOODS,
            self::CUSTOM_INFO_SHIPMENT_TYPE_DOCUMENTS,
            self::CUSTOM_INFO_SHIPMENT_TYPE_OTHER,
        );
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
        $tagName = 'customsInfo';
        if ($prefix !== null) {
            $tagName = $prefix . ':' . $tagName;
        }

        $customsInfo = $document->createElement($tagName);

        if ($this->getParcelValue() !== null) {
            $tagName = 'parcelValue';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $customsInfo->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getParcelValue()
                )
            );
        }
        if ($this->getContentDescription() !== null) {
            $tagName = 'contentDescription';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $customsInfo->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getContentDescription()
                )
            );
        }
        if ($this->getShipmentType() !== null) {
            $tagName = 'shipmentType';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $customsInfo->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getShipmentType()
                )
            );
        }
        if ($this->getPossibleParcelReturnInstructionValues() !== null) {
            $tagName = 'parcelReturnInstructions';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $customsInfo->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getParcelReturnInstructions()
                )
            );
        }
        if ($this->getPrivateAddress() !== null) {
            $tagName = 'privateAddress';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            if ($this->getPrivateAddress()) {
                $value = 'true';
            } else {
                $value = 'false';
            }
            $customsInfo->appendChild(
                $document->createElement(
                    $tagName,
                    $value
                )
            );
        }

        return $customsInfo;
    }

    /**
     * @param  \SimpleXMLElement $xml
     *
     * @return CustomsInfo
     * @throws BpostInvalidLengthException
     * @throws BpostInvalidValueException
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        $customsInfo = new CustomsInfo();

        if (isset($xml->parcelValue) && $xml->parcelValue != '') {
            $customsInfo->setParcelValue(
                (int) $xml->parcelValue
            );
        }
        if (isset($xml->contentDescription) && $xml->contentDescription != '') {
            $customsInfo->setContentDescription(
                (string) $xml->contentDescription
            );
        }
        if (isset($xml->shipmentType) && $xml->shipmentType != '') {
            $customsInfo->setShipmentType(
                (string) $xml->shipmentType
            );
        }
        if (isset($xml->parcelReturnInstructions) && $xml->parcelReturnInstructions != '') {
            $customsInfo->setParcelReturnInstructions(
                (string) $xml->parcelReturnInstructions
            );
        }
        if (isset($xml->privateAddress) && $xml->privateAddress != '') {
            $customsInfo->setPrivateAddress(
                ((string) $xml->privateAddress == 'true')
            );
        }

        return $customsInfo;
    }
}
