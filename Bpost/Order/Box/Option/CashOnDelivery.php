<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box\Option;

use TijsVerkoyen\Bpost\Exception;

/**
 * bPost CashOnDelivery class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class CashOnDelivery extends Option
{
    /**
     * @var float
     */
    private $amount;

    /**
     * @var string
     */
    private $iban;

    /**
     * @var string
     */
    private $bic;

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $bic
     */
    public function setBic($bic)
    {
        $this->bic = $bic;
    }

    /**
     * @return string
     */
    public function getBic()
    {
        return $this->bic;
    }

    /**
     * @param string $iban
     */
    public function setIban($iban)
    {
        $this->iban = $iban;
    }

    /**
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * @param float  $amount
     * @param string $iban
     * @param string $bic
     */
    public function __construct($amount, $iban, $bic)
    {
        $this->setAmount($amount);
        $this->setIban($iban);
        $this->setBic($bic);
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @return array
     */
    public function toXMLArray()
    {
        $data = array();
        if ($this->getAmount() !== null) {
            $data['codAmount'] = $this->getAmount();
        }
        if ($this->getIban() !== null) {
            $data['iban'] = $this->getIban();
        }
        if ($this->getBic() !== null) {
            $data['bic'] = $this->getBic();
        }

        return array('cod' => $data);
    }
}
