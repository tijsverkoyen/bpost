<?php
namespace TijsVerkoyen\Bpost\Bpack247;

use TijsVerkoyen\Bpost\Exception;
use TijsVerkoyen\Bpost\Bpack247\CustomerPackStation;

/**
 * bPost Customer class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Customer
{
    /**
     * @var bool
     */
    private $activated;

    /**
     * @var string
     */
    private $userID;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $companyName;

    /**
     * @var string
     */
    private $street;

    /**
     * @var string
     */
    private $number;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $mobilePrefix = '0032';

    /**
     * @var string
     */
    private $mobileNumber;

    /**
     * @var string
     */
    private $postalCode;

    /**
     * @var array
     */
    private $packStations = array();

    /**
     * @var string
     */
    private $town;

    /**
     * @var string
     */
    private $preferredLanguage;

    /**
     * @var string
     */
    private $title;

    /**
     * @var bool
     */
    private $isComfortZoneUser;

    /**
     * @var \DateTime
     */
    private $dateOfBirth;

    /**
     * @var string
     */
    private $deliveryCode;

    /**
     * @var bool
     */
    private $optIn;

    /**
     * @var bool
     */
    private $receivePromotions;

    /**
     * @var bool
     */
    private $useInformationForThirdParty;

    /**
     * @var string
     */
    private $userName;

    /**
     * @param boolean $activated
     */
    public function setActivated($activated)
    {
        $this->activated = $activated;
    }

    /**
     * @return boolean
     */
    public function getActivated()
    {
        return $this->activated;
    }

    /**
     * @param string $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }

    /**
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param \DateTime $dateOfBirth
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * @param string $deliveryCode
     */
    public function setDeliveryCode($deliveryCode)
    {
        $this->deliveryCode = $deliveryCode;
    }

    /**
     * @return string
     */
    public function getDeliveryCode()
    {
        return $this->deliveryCode;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param boolean $isComfortZoneUser
     */
    public function setIsComfortZoneUser($isComfortZoneUser)
    {
        $this->isComfortZoneUser = $isComfortZoneUser;
    }

    /**
     * @return boolean
     */
    public function getIsComfortZoneUser()
    {
        return $this->isComfortZoneUser;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $mobileNumber
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;
    }

    /**
     * @return string
     */
    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }

    /**
     * @param string $mobilePrefix
     */
    public function setMobilePrefix($mobilePrefix)
    {
        $this->mobilePrefix = $mobilePrefix;
    }

    /**
     * @return string
     */
    public function getMobilePrefix()
    {
        return $this->mobilePrefix;
    }

    /**
     * @param string $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param boolean $optIn
     */
    public function setOptIn($optIn)
    {
        $this->optIn = $optIn;
    }

    /**
     * @return boolean
     */
    public function getOptIn()
    {
        return $this->optIn;
    }

    /**
     * @param CustomerPackStation $packStation
     */
    public function addPackStation(CustomerPackStation $packStation)
    {
        $this->packStations[] = $packStation;
    }

    /**
     * @param array $packStations
     */
    public function setPackStations($packStations)
    {
        $this->packStations = $packStations;
    }

    /**
     * @return array
     */
    public function getPackStations()
    {
        return $this->packStations;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $preferredLanguage
     */
    public function setPreferredLanguage($preferredLanguage)
    {
        if (!in_array($preferredLanguage, self::getPossiblePreferredLanguageValues())) {
            throw new Exception(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', self::getPossiblePreferredLanguageValues())
                )
            );
        }

        $this->preferredLanguage = $preferredLanguage;
    }

    /**
     * @return string
     */
    public function getPreferredLanguage()
    {
        return $this->preferredLanguage;
    }

    /**
     * @return array
     */
    public static function getPossiblePreferredLanguageValues()
    {
        return array(
            'nl-BE',
            'fr-BE',
            'en-US',
        );
    }

    /**
     * @param boolean $receivePromotions
     */
    public function setReceivePromotions($receivePromotions)
    {
        $this->receivePromotions = $receivePromotions;
    }

    /**
     * @return boolean
     */
    public function getReceivePromotions()
    {
        return $this->receivePromotions;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        if (!in_array($title, self::getPossibleTitleValues())) {
            throw new Exception(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', self::getPossibleTitleValues())
                )
            );
        }

        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return array
     */
    public static function getPossibleTitleValues()
    {
        return array(
            'Mr.',
            'Ms.',
        );
    }

    /**
     * @param string $town
     */
    public function setTown($town)
    {
        $this->town = $town;
    }

    /**
     * @return string
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * @param boolean $useInformationForThirdParty
     */
    public function setUseInformationForThirdParty($useInformationForThirdParty)
    {
        $this->useInformationForThirdParty = $useInformationForThirdParty;
    }

    /**
     * @return boolean
     */
    public function getUseInformationForThirdParty()
    {
        return $this->useInformationForThirdParty;
    }

    /**
     * @param string $userID
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;
    }

    /**
     * @return string
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * @param string $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @param  \DOMDocument $document
     * @return \DOMElement
     */
    public function toXML(\DOMDocument $document)
    {
        $customer = $document->createElement(
            'Customer'
        );
        $customer->setAttribute(
            'xmlns',
            'http://schema.post.be/ServiceController/customer'
        );
        $customer->setAttribute(
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance'
        );
        $customer->setAttribute(
            'xsi:schemaLocation',
            'http://schema.post.be/ServiceController/customer'
        );

        $document->appendChild($customer);

        if ($this->getFirstName() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'FirstName',
                    $this->getFirstName()
                )
            );
        }
        if ($this->getLastName() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'LastName',
                    $this->getLastName()
                )
            );
        }
        if ($this->getStreet() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'Street',
                    $this->getStreet()
                )
            );
        }
        if ($this->getNumber() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'Number',
                    $this->getNumber()
                )
            );
        }
        if ($this->getEmail() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'Email',
                    $this->getEmail()
                )
            );
        }
        if ($this->getMobilePrefix() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'MobilePrefix',
                    $this->getMobilePrefix()
                )
            );
        }
        if ($this->getMobileNumber() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'MobileNumber',
                    $this->getMobileNumber()
                )
            );
        }
        if ($this->getPostalCode() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'PostalCode',
                    $this->getPostalCode()
                )
            );
        }
        if ($this->getPreferredLanguage() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'PreferredLanguage',
                    $this->getPreferredLanguage()
                )
            );
        }
        if ($this->getTitle() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'Title',
                    $this->getTitle()
                )
            );
        }

        return $customer;
    }

    /**
     * @param  \SimpleXMLElement $xml
     * @return Customer
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        // @todo work with classmaps ...
        if (!isset($xml->UserID)) {
            throw new Exception('No UserId found.');
        }

        $customer = new Customer();

        if (isset($xml->UserID) && $xml->UserID != '') {
            $customer->setUserID((string) $xml->UserID);
        }
        if (isset($xml->FirstName) && $xml->FirstName != '') {
            $customer->setFirstName((string) $xml->FirstName);
        }
        if (isset($xml->LastName) && $xml->LastName != '') {
            $customer->setLastName((string) $xml->LastName);
        }
        if (isset($xml->Street) && $xml->Street != '') {
            $customer->setStreet((string) $xml->Street);
        }
        if (isset($xml->Number) && $xml->Number != '') {
            $customer->setNumber((string) $xml->Number);
        }
        if (isset($xml->CompanyName) && $xml->CompanyName != '') {
            $customer->setCompanyName((string) $xml->CompanyName);
        }
        if (isset($xml->DateOfBirth) && $xml->DateOfBirth != '') {
            $dateTime = new \DateTime((string) $xml->DateOfBirth);
            $customer->setDateOfBirth($dateTime);
        }
        if (isset($xml->DeliveryCode) && $xml->DeliveryCode != '') {
            $customer->setDeliveryCode(
                (string) $xml->DeliveryCode
            );
        }
        if (isset($xml->Email) && $xml->Email != '') {
            $customer->setEmail((string) $xml->Email);
        }
        if (isset($xml->MobilePrefix) && $xml->MobilePrefix != '') {
            $customer->setMobilePrefix(
                trim((string) $xml->MobilePrefix)
            );
        }
        if (isset($xml->MobileNumber) && $xml->MobileNumber != '') {
            $customer->setMobileNumber(
                (string) $xml->MobileNumber
            );
        }
        if (isset($xml->Postalcode) && $xml->Postalcode != '') {
            $customer->setPostalCode(
                (string) $xml->Postalcode
            );
        }
        if (isset($xml->PreferredLanguage) && $xml->PreferredLanguage != '') {
            $customer->setPreferredLanguage(
                (string) $xml->PreferredLanguage
            );
        }
        if (isset($xml->ReceivePromotions) && $xml->ReceivePromotions != '') {
            $receivePromotions = in_array((string) $xml->ReceivePromotions, array('true', '1'));
            $customer->setReceivePromotions($receivePromotions);
        }
        if (isset($xml->actived) && $xml->actived != '') {
            $activated = in_array((string) $xml->actived, array('true', '1'));
            $customer->setActivated($activated);
        }
        if (isset($xml->Title) && $xml->Title != '') {
            $title = (string) $xml->Title;
            $title = ucfirst(strtolower($title));
            if (substr($title, -1) != '.') {
                $title .= '.';
            }

            $customer->setTitle($title);
        }
        if (isset($xml->Town) && $xml->Town != '') {
            $customer->setTown((string) $xml->Town);
        }

        if (isset($xml->PackStations->CustomerPackStation)) {
            foreach ($xml->PackStations->CustomerPackStation as $packStation) {
                $customer->addPackStation(CustomerPackStation::createFromXML($packStation));
            }
        }

        return $customer;
    }
}
