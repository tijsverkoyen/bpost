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
     * @return array
     */
    public function toXMLArray()
    {
        $data = parent::toXMLArray();
        if ($this->getPugoId() !== null) {
            $data['pugoId'] = $this->getPugoId();
        }
        if ($this->getPugoName() !== null) {
            $data['pugoName'] = $this->getPugoName();
        }
        if ($this->getPugoAddress() !== null) {
            $data['pugoAddress'] = $this->getPugoAddress()->toXMLArray();
        }
        if ($this->getReceiverName() !== null) {
            $data['receiverName'] = $this->getReceiverName();
        }
        if ($this->getReceiverCompany() !== null) {
            $data['receiverCompany'] = $this->getReceiverCompany();
        }

        return array('atBpost' => $data);
    }
}
