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
     * @param \TijsVerkoyen\Bpost\Bpost\Order\Box\ParcelsDepotAddress $parcelsDepotAddress
     */
    public function setParcelsDepotAddress($parcelsDepotAddress)
    {
        $this->parcelsDepotAddress = $parcelsDepotAddress;
    }

    /**
     * @return \TijsVerkoyen\Bpost\Bpost\Order\Box\ParcelsDepotAddress
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
     * @return array
     */
    public function toXMLArray()
    {
        $data = parent::toXMLArray();
        if ($this->getParcelsDepotId() !== null) {
            $data['parcelsDepotId'] = $this->getParcelsDepotId();
        }
        if ($this->getParcelsDepotName() !== null) {
            $data['parcelsDepotName'] = $this->getParcelsDepotName();
        }
        if ($this->getParcelsDepotAddress() !== null) {
            $data['parcelsDepotAddress'] = $this->getParcelsDepotAddress()->toXMLArray();
        }
        if ($this->getMemberId() !== null) {
            $data['memberId'] = $this->getMemberId();
        }
        if ($this->getReceiverName() !== null) {
            $data['receiverName'] = $this->getReceiverName();
        }
        if ($this->getReceiverCompany() !== null) {
            $data['receiverCompany'] = $this->getReceiverCompany();
        }

        return array('at24-7' => $data);
    }
}
