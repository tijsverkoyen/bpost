<?php
namespace TijsVerkoyen\Bpost;

/**
 * bPost International Label Info class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class InternationalLabelInfo
{
    /**
     * Generic info
     *
     * @var string
     */
    private $contentDescription, $shipmentType, $parcelReturnInstructions;

    /**
     * Generic info
     *
     * @var int
     */
    private $parcelValue, $parcelWeight;

    /**
     * Generic info
     *
     * @var bool
     */
    private $privateAddress;

    /**
     * @param int            $parcelValue              The value of the parcel in euro cent
     * @param int            $parcelWeight             The weight of the parcel in grams
     * @param string         $contentDescription       The content description
     * @param string         $shipmentType             The shipment type, allowed values are: SAMPLE, GIFT, OTHER, DOCUMENT
     * @param string         $parcelReturnInstructions The return instructions, allowed values are: RTA, ABANDONED, RTS
     * @param bool[optional] $privateAddress           Is the address a private address?
     */
    public function __construct($parcelValue, $parcelWeight, $contentDescription, $shipmentType, $parcelReturnInstructions, $privateAddress = true)
    {
        $this->setParcelValue($parcelValue);
        $this->setParcelWeight($parcelWeight);
        $this->setContentDescription($contentDescription);
        $this->setShipmentType($shipmentType);
        $this->setParcelReturnInstructions($parcelReturnInstructions);
        $this->setPrivateAddress($privateAddress);
    }

    /**
     * Get the content description
     *
     * @return string
     */
    public function getContentDescription()
    {
        return $this->contentDescription;
    }

    /**
     * Get the parcel return instructions
     *
     * @return string
     */
    public function getParcelReturnInstructions()
    {
        return $this->parcelReturnInstructions;
    }

    /**
     * Get the parcel value in euro cents
     *
     * @return string
     */
    public function getParcelValue()
    {
        return $this->parcelValue;
    }

    /**
     * Get the parcel weight in grams
     *
     * @return int
     */
    public function getParcelWeight()
    {
        return $this->parcelWeight;
    }

    /**
     * Is the address a private address?
     *
     * @return bool
     */
    public function getPrivateAddress()
    {
        return $this->privateAddress;
    }

    /**
     * Get the shipment type
     *
     * @return string
     */
    public function getShipmentType()
    {
        return $this->shipmentType;
    }

    /**
     * Get the content description
     *
     * @param string $contentDescription
     */
    public function setContentDescription($contentDescription)
    {
        $this->contentDescription = (string) $contentDescription;
    }

    /**
     * The return instructions
     *
     * @param string $parcelRetrurnInstructions Allowed values are: RTA, ABANDONED, RTS.
     */
    public function setParcelReturnInstructions($parcelReturnInstructions)
    {
        $allowedParcelReturnInstructions = array('RTA', 'ABANDONED', 'RTS');

        // validate
        if (!in_array($parcelReturnInstructions, $allowedParcelReturnInstructions)) {
            throw new Exception(
                'Invalid value for parcelReturnInstructions (' . $parcelReturnInstructions . '), allowed values are: ' .
                implode(',  ', $allowedParcelReturnInstructions) . '.'
            );
        }
        $this->parcelReturnInstructions = (string) $parcelReturnInstructions;
    }

    /**
     * The value of the parce in Euro cent
     *
     * @param int $parcelValue
     */
    public function setParcelValue($parcelValue)
    {
        $this->parcelValue = (int) $parcelValue;
    }

    /**
     * The weight of the parcel in grams
     *
     * @param int $parcelWeight
     */
    public function setParcelWeight($parcelWeight)
    {
        $this->parcelWeight = (int) $parcelWeight;
    }

    /**
     * Is the address a private address?
     *
     * @param bool $privateAddress
     */
    public function setPrivateAddress($privateAddress)
    {
        $this->privateAddress = (bool) $privateAddress;
    }

    /**
     * Set the shipment type
     *
     * @param string $shipmentType Allowed values are: SAMPLE, GIFT, OTHER, DOCUMENTS
     */
    public function setShipmentType($shipmentType)
    {
        $allowedShipmentTypes = array('SAMPLE', 'GIFT', 'OTHER', 'DOCUMENTS');

        // validate
        if (!in_array($shipmentType, $allowedShipmentTypes)) {
            throw new Exception(
                'Invalid value for shipmentType (' . $shipmentType . '), allowed values are: ' .
                implode(',  ', $allowedShipmentTypes) . '.'
            );
        }
        $this->shipmentType = (string) $shipmentType;
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @return array
     */
    public function toXMLArray()
    {
        $data = array();
        $data['parcelValue'] = $this->parcelValue;
        $data['parcelWeight'] = $this->parcelWeight;
        $data['contentDescription'] = $this->contentDescription;
        $data['shipmentType'] = $this->shipmentType;
        $data['parcelReturnInstructions'] = $this->parcelReturnInstructions;
        $data['privateAddress'] = $this->privateAddress;

        return $data;
    }
}
