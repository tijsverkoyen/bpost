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
    const TAG_NAME = 'customer';

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
     * @param \DomDocument
     * @param  string      $prefix
     * @return \DomElement
     */
    public function toXML(\DomDocument $document, $prefix = null)
    {
        $tagName = static::TAG_NAME;
        if ($prefix !== null) {
            $tagName = $prefix . ':' . $tagName;
        }

        $customer = $document->createElement($tagName);

        if ($this->getName() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'common:name',
                    $this->getName()
                )
            );
        }
        if ($this->getCompany() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'common:company',
                    $this->getCompany()
                )
            );
        }
        if ($this->getAddress() !== null) {
            $customer->appendChild(
                $this->getAddress()->toXML($document)
            );
        }
        if ($this->getEmailAddress() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'common:emailAddress',
                    $this->getEmailAddress()
                )
            );
        }
        if ($this->getPhoneNumber() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'common:phoneNumber',
                    $this->getPhoneNumber()
                )
            );
        }

        return $customer;
    }

    /**
     * @param  \SimpleXMLElement $xml
     * @param  Customer          $instance
     * @return Customer
     */
    public static function createFromXMLHelper(\SimpleXMLElement $xml, Customer $instance)
    {
        if (isset($xml->name) && $xml->name != '') {
            $instance->setName((string) $xml->name);
        }
        if (isset($xml->company) && $xml->company != '') {
            $instance->setCompany((string) $xml->company);
        }
        if (isset($xml->address)) {
            $instance->setAddress(
                Address::createFromXML($xml->address)
            );
        }
        if (isset($xml->emailAddress) && $xml->emailAddress != '') {
            $instance->setEmailAddress(
                (string) $xml->emailAddress
            );
        }
        if (isset($xml->phoneNumber) && $xml->phoneNumber != '') {
            $instance->setPhoneNumber((string) $xml->phoneNumber);
        }

        return $instance;
    }
}
