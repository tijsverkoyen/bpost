<?php
namespace TijsVerkoyen\Bpost\Bpack247;

/**
 * bPost Order class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Customer
{
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
     * Return the object as an array for usage in the API
     *
     * @return array
     */
    public function toXMLArray()
    {
        $data = array();

        if ($this->getFirstName() !== null) {
            $data['FirstName'] = $this->getFirstName();
        }
        if ($this->getLastName() !== null) {
            $data['LastName'] = $this->getLastName();
        }
        if ($this->getStreet() !== null) {
            $data['Street'] = $this->getStreet();
        }
        if ($this->getNumber() !== null) {
            $data['Number'] = $this->getNumber();
        }
        if ($this->getEmail() !== null) {
            $data['Email'] = $this->getEmail();
        }
        if ($this->getMobilePrefix() !== null) {
            $data['MobilePrefix'] = $this->getMobilePrefix();
        }
        if ($this->getMobileNumber() !== null) {
            $data['MobileNumber'] = $this->getMobileNumber();
        }
        if ($this->getPostalCode() !== null) {
            $data['PostalCode'] = $this->getPostalCode();
        }
        if ($this->getPreferredLanguage() !== null) {
            $data['PreferredLanguage'] = $this->getPreferredLanguage();
        }
        if ($this->getTitle() !== null) {
            $data['Title'] = $this->getTitle();
        }

        return $data;
    }
}
