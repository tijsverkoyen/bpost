<?php
namespace TijsVerkoyen\Bpost\Bpost\Order;

use TijsVerkoyen\Bpost\Exception\BpostLogicException\BpostInvalidValueException;
use TijsVerkoyen\Bpost\Exception\BpostNotImplementedException;

/**
 * bPost Box class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Box
{

    const BOX_STATUS_OPEN = 'OPEN';
    const BOX_STATUS_PENDING = 'PENDING';
    const BOX_STATUS_PRINTED = 'PRINTED';
    const BOX_STATUS_CANCELLED = 'CANCELLED';
    const BOX_STATUS_ON_HOLD = 'ON-HOLD';
    const BOX_STATUS_ANNOUNCED = 'ANNOUNCED';
    const BOX_STATUS_IN_TRANSIT = 'IN_TRANSIT';
    const BOX_STATUS_AWAITING_PICKUP = 'AWAITING_PICKUP';
    const BOX_STATUS_DELIVERED = 'DELIVERED';
    const BOX_STATUS_BACK_TO_SENDER = 'BACK_TO_SENDER';

    /**
     * @var \TijsVerkoyen\Bpost\Bpost\Order\Sender
     */
    private $sender;

    /**
     * @var \TijsVerkoyen\Bpost\Bpost\Order\Box\AtHome
     */
    private $nationalBox;

    /**
     * @var \TijsVerkoyen\Bpost\Bpost\Order\Box\International
     */
    private $internationalBox;

    /**
     * @var string
     */
    private $remark;

    /**
     * @var string
     */
    private $status;

    /** @var string */
    private $barcode;

    /** @var string */
    private $additionalCustomerReference;

    /**
     * @param \TijsVerkoyen\Bpost\Bpost\Order\Box\International $internationalBox
     */
    public function setInternationalBox(Box\International $internationalBox)
    {
        $this->internationalBox = $internationalBox;
    }

    /**
     * @return \TijsVerkoyen\Bpost\Bpost\Order\Box\International
     */
    public function getInternationalBox()
    {
        return $this->internationalBox;
    }

    /**
     * @param \TijsVerkoyen\Bpost\Bpost\Order\Box\National $nationalBox
     */
    public function setNationalBox(Box\National $nationalBox)
    {
        $this->nationalBox = $nationalBox;
    }

    /**
     * @return \TijsVerkoyen\Bpost\Bpost\Order\Box\National
     */
    public function getNationalBox()
    {
        return $this->nationalBox;
    }

    /**
     * @param string $remark
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;
    }

    /**
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * @param \TijsVerkoyen\Bpost\Bpost\Order\Sender $sender
     */
    public function setSender(Sender $sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return \TijsVerkoyen\Bpost\Bpost\Order\Sender
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param string $status
     * @throws BpostInvalidValueException
     */
    public function setStatus($status)
    {
        $status = strtoupper($status);
        if (!in_array($status, self::getPossibleStatusValues())) {
            throw new BpostInvalidValueException('status', $status, self::getPossibleStatusValues());
        }

        $this->status = $status;
    }

    /**
     * @param string $barcode
     */
    public function setBarcode($barcode)
    {
        $this->barcode = strtoupper((string) $barcode);
    }

    /**
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $additionalCustomerReference
     */
    public function setAdditionalCustomerReference($additionalCustomerReference)
    {
        $this->additionalCustomerReference = (string)$additionalCustomerReference;
    }

    /**
     * @return string
     */
    public function getAdditionalCustomerReference()
    {
        return $this->additionalCustomerReference;
    }

    /**
     * @return array
     */
    public static function getPossibleStatusValues()
    {
        return array(
            self::BOX_STATUS_OPEN,
            self::BOX_STATUS_PENDING,
            self::BOX_STATUS_PRINTED,
            self::BOX_STATUS_CANCELLED,
            self::BOX_STATUS_ON_HOLD,
            self::BOX_STATUS_ANNOUNCED,
            self::BOX_STATUS_IN_TRANSIT,
            self::BOX_STATUS_AWAITING_PICKUP,
            self::BOX_STATUS_DELIVERED,
            self::BOX_STATUS_BACK_TO_SENDER,
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
        $tagName = 'box';
        if ($prefix !== null) {
            $tagName = $prefix . ':' . $tagName;
        }

        $box = $document->createElement($tagName);

        if ($this->getSender() !== null) {
            $box->appendChild(
                $this->getSender()->toXML($document, $prefix)
            );
        }
        if ($this->getNationalBox() !== null) {
            $box->appendChild(
                $this->getNationalBox()->toXML($document, $prefix)
            );
        }
        if ($this->getInternationalBox() !== null) {
            $box->appendChild(
                $this->getInternationalBox()->toXML($document, $prefix)
            );
        }
        if ($this->getRemark() !== null) {
            $tagName = 'remark';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $box->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getRemark()
                )
            );
        }
        if ($this->getAdditionalCustomerReference() !== null) {
            $tagName = 'additionalCustomerReference';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $box->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getAdditionalCustomerReference()
                )
            );
        }
        if ($this->getBarcode() !== null) {
            $tagName = 'barcode';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $box->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getBarcode()
                )
            );
        }

        return $box;
    }

    /**
     * @param  \SimpleXMLElement $xml
     *
     * @return Box
     * @throws BpostInvalidValueException
     * @throws BpostNotImplementedException
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        $box = new Box();
        if (isset($xml->sender)) {
            $box->setSender(
                Sender::createFromXML(
                    $xml->sender->children(
                        'http://schema.post.be/shm/deepintegration/v3/common'
                    )
                )
            );
        }
        if (isset($xml->nationalBox)) {
            /** @var \SimpleXMLElement $nationalBoxData */
            $nationalBoxData = $xml->nationalBox->children('http://schema.post.be/shm/deepintegration/v3/national');

            // build classname based on the tag name
            $className = '\\TijsVerkoyen\\Bpost\\Bpost\\Order\\Box\\' . ucfirst($nationalBoxData->getName());
            if ($nationalBoxData->getName() == 'at24-7') {
                $className = '\\TijsVerkoyen\\Bpost\\Bpost\\Order\\Box\\At247';
            }

            if (!method_exists($className, 'createFromXML')) {
                throw new BpostNotImplementedException();
            }

            $nationalBox = call_user_func(
                array($className, 'createFromXML'),
                $nationalBoxData
            );

            $box->setNationalBox($nationalBox);
        }
        if (isset($xml->internationalBox)) {
            /** @var \SimpleXMLElement $internationalBoxData */
            $internationalBoxData = $xml->internationalBox->children('http://schema.post.be/shm/deepintegration/v3/international');

            // build classname based on the tag name
            $className = '\\TijsVerkoyen\\Bpost\\Bpost\\Order\\Box\\' . ucfirst($internationalBoxData->getName());

            if (!method_exists($className, 'createFromXML')) {
                var_dump($className);
                throw new BpostNotImplementedException();
            }

            $internationalBox = call_user_func(
                array($className, 'createFromXML'),
                $internationalBoxData
            );

            $box->setInternationalBox($internationalBox);
        }
        if (isset($xml->remark) && $xml->remark != '') {
            $box->setRemark((string) $xml->remark);
        }
        if (isset($xml->additionalCustomerReference) && $xml->additionalCustomerReference != '') {
            $box->setAdditionalCustomerReference((string)$xml->additionalCustomerReference);
        }
        if (!empty($xml->barcode)) {
            $box->setBarcode((string) $xml->barcode);
        }
        if (isset($xml->status) && $xml->status != '') {
            $box->setStatus((string) $xml->status);
        }

        return $box;
    }
}
