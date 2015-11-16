<?php
namespace TijsVerkoyen\Bpost\Bpack247;

/**
 * bPost Customer Pack Station class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class CustomerPackStation
{
    /**
     * @var string
     */
    private $customLabel;

    /**
     * @var string
     */
    private $orderNumber;

    /**
     * @var string
     */
    private $packstationId;

    /**
     * @param string $customLabel
     */
    public function setCustomLabel($customLabel)
    {
        $this->customLabel = $customLabel;
    }

    /**
     * @return string
     */
    public function getCustomLabel()
    {
        return $this->customLabel;
    }

    /**
     * @param string $orderNumber
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;
    }

    /**
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * @param string $packstationId
     */
    public function setPackstationId($packstationId)
    {
        $this->packstationId = $packstationId;
    }

    /**
     * @return string
     */
    public function getPackstationId()
    {
        return $this->packstationId;
    }

    /**
     * @param  \SimpleXMLElement   $xml
     * @return CustomerPackStation
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        $packStation = new CustomerPackStation();

        if (isset($xml->OrderNumber) && $xml->OrderNumber != '') {
            $packStation->setOrderNumber((string) $xml->OrderNumber);
        }
        if (isset($xml->CustomLabel) && $xml->CustomLabel != '') {
            $packStation->setCustomLabel((string) $xml->CustomLabel);
        }
        if (isset($xml->PackstationID) && $xml->PackstationID != '') {
            $packStation->setPackstationId((string) $xml->PackstationID);
        }

        return $packStation;
    }
}
