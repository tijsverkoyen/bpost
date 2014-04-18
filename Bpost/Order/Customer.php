<?php
namespace TijsVerkoyen\Bpost\Bpost\Order;

use TijsVerkoyen\Bpost\Exception;

/**
 * bPost Customer class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Customer
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $company;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var string
     */
    private $emailAddress;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @param \TijsVerkoyen\Bpost\Bpost\Order\Address $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return \TijsVerkoyen\Bpost\Bpost\Order\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $emailAddress
     */
    public function setEmailAddress($emailAddress)
    {
        $length = 50;
        if (mb_strlen($emailAddress) > $length) {
            throw new Exception(sprintf('Invalid length, maximum is %1$s.', $length));
        }
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $length = 20;
        if (mb_strlen($phoneNumber) > $length) {
            throw new Exception(sprintf('Invalid length, maximum is %1$s.', $length));
        }
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @return array
     */
    public function toXMLArray()
    {
        $data = array();
        if ($this->getName() !== null) {
            $data['name'] = $this->getName();
        }
        if ($this->getCompany() !== null) {
            $data['company'] = $this->getCompany();
        }
        if ($this->getAddress() !== null) {
            $data['address'] = $this->getAddress()->toXMLArray();
        }
        if ($this->getEmailAddress() !== null) {
            $data['emailAddress'] = $this->getEmailAddress();
        }
        if ($this->getPhoneNumber() !== null) {
            $data['phoneNumber'] = $this->getPhoneNumber();
        }

        return $data;
    }
}
