<?php
namespace TijsVerkoyen\Bpost;

/**
 * bPost Address class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Address
{
    /**
     * Generic info
     *
     * @var string
     */
    private $streetName, $number, $box, $postcalCode, $locality, $countryCode;

    /**
     * Create a Address object
     *
     * @param string           $streetName
     * @param string           $number
     * @param string           $postalCode
     * @param string           $locality
     * @param string[optional] $countryCode
     */
    public function __construct($streetName, $number, $postalCode, $locality, $countryCode = 'BE')
    {
        $this->setStreetName($streetName);
        $this->setNumber($number);
        $this->setPostcalCode($postalCode);
        $this->setLocality($locality);
        $this->setCountryCode($countryCode);
    }

    /**
     * Get the box
     *
     * @return string
     */
    public function getBox()
    {
        return $this->box;
    }

    /**
     * Get the country code
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Get the locality
     *
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Get the number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Get the postal code
     *
     * @return string
     */
    public function getPostcalCode()
    {
        return $this->postcalCode;
    }

    /**
     * Get the street name
     *
     * @return string
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * Set the box
     *
     * @param string $box
     */
    public function setBox($box)
    {
        if(mb_strlen($box) > 8) throw new Exception('Invalid length for box, maximum is 8.');
        $this->box = $box;
    }

    /**
     * Set the country code
     *
     * @param string $countryCode
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * Set the locality
     *
     * @param string $locality
     */
    public function setLocality($locality)
    {
        if(mb_strlen($locality) > 40) throw new Exception('Invalid length for locality, maximum is 40.');
        $this->locality = $locality;
    }

    /**
     * Set the number
     *
     * @param string $number
     */
    public function setNumber($number)
    {
        if(mb_strlen($number) > 8) throw new Exception('Invalid length for number, maximum is 8.');
        $this->number = $number;
    }

    /**
     * Set the postal code
     *
     * @param string $postcalCode
     */
    public function setPostcalCode($postcalCode)
    {
        if(mb_strlen($postcalCode) > 8) throw new Exception('Invalid length for postalCode, maximum is 8.');
        $this->postcalCode = $postcalCode;
    }

    /**
     * Set the street name
     * @param string $streetName
     */
    public function setStreetName($streetName)
    {
        if(mb_strlen($streetName) > 40) throw new Exception('Invalid length for streetName, maximum is 40.');
        $this->streetName = $streetName;
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @return array
     */
    public function toXMLArray()
    {
        $data = array();
        if($this->streetName !== null) $data['streetName'] = $this->streetName;
        if($this->number !== null) $data['number'] = $this->number;
        if($this->box !== null) $data['box'] = $this->box;
        if($this->postcalCode !== null) $data['postalCode'] = $this->postcalCode;
        if($this->locality !== null) $data['locality'] = $this->locality;
        if($this->countryCode !== null) $data['countryCode'] = $this->countryCode;

        return $data;
    }
}
