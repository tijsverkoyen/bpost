<?php
namespace TijsVerkoyen\Bpost\Bpost\Order;

use TijsVerkoyen\Bpost\Exception;

/**
 * bPost Address class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Address
{
    const TAG_NAME = 'common:address';

    /**
     * @var string
     */
    private $streetName;

    /**
     * @var string
     */
    private $number;

    /**
     * @var string
     */
    private $box;

    /**
     * @var string
     */
    private $postalCode;

    /**
     * @var string
     */
    private $locality;

    /**
     * @var string
     */
    private $countryCode = 'BE';

    /**
     * @param string $box
     */
    public function setBox($box)
    {
        $length = 8;
        if (mb_strlen($box) > $length) {
            throw new Exception(sprintf('Invalid length, maximum is %1$s.', $length));
        }
        $this->box = $box;
    }

    /**
     * @return string
     */
    public function getBox()
    {
        return $this->box;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode($countryCode)
    {
        $length = 2;
        if (mb_strlen($countryCode) > $length) {
            throw new Exception(sprintf('Invalid length, maximum is %1$s.', $length));
        }
        $this->countryCode = strtoupper($countryCode);
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param string $locality
     */
    public function setLocality($locality)
    {
        $length = 40;
        if (mb_strlen($locality) > $length) {
            throw new Exception(sprintf('Invalid length, maximum is %1$s.', $length));
        }
        $this->locality = $locality;
    }

    /**
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * @param string $number
     */
    public function setNumber($number)
    {
        $length = 8;
        if (mb_strlen($number) > $length) {
            throw new Exception(sprintf('Invalid length, maximum is %1$s.', $length));
        }
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
     * @param string $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $length = 40;
        if (mb_strlen($postalCode) > $length) {
            throw new Exception(sprintf('Invalid length, maximum is %1$s.', $length));
        }
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
     * @param string $streetName
     */
    public function setStreetName($streetName)
    {
        $length = 40;
        if (mb_strlen($streetName) > $length) {
            throw new Exception(sprintf('Invalid length, maximum is %1$s.', $length));
        }
        $this->streetName = $streetName;
    }

    /**
     * @return string
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * @param string $streetName
     * @param string $number
     * @param string $box
     * @param string $postalCode
     * @param string $locality
     * @param string $countryCode
     */
    public function __construct(
        $streetName = null,
        $number = null,
        $box = null,
        $postalCode = null,
        $locality = null,
        $countryCode = null
    ) {
        if ($streetName != null) {
            $this->setStreetName($streetName);
        }
        if ($number != null) {
            $this->setNumber($number);
        }
        if ($box != null) {
            $this->setBox($box);
        }
        if ($postalCode != null) {
            $this->setPostalCode($postalCode);
        }
        if ($locality != null) {
            $this->setLocality($locality);
        }
        if ($countryCode != null) {
            $this->setCountryCode($countryCode);
        }
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @param  \DOMDocument $document
     * @param  string       $prefix
     * @return \DOMElement
     */
    public function toXML(\DOMDocument $document, $prefix = 'common')
    {
        $tagName = static::TAG_NAME;
        $address = $document->createElement($tagName);
        $document->appendChild($address);

        if ($this->getStreetName() !== null) {
            $tagName = 'streetName';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $address->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getStreetName()
                )
            );
        }
        if ($this->getNumber() !== null) {
            $tagName = 'number';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $address->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getNumber()
                )
            );
        }
        if ($this->getBox() !== null) {
            $tagName = 'box';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $address->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getBox()
                )
            );
        }
        if ($this->getPostalCode() !== null) {
            $tagName = 'postalCode';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $address->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getPostalCode()
                )
            );
        }
        if ($this->getLocality() !== null) {
            $tagName = 'locality';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $address->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getLocality()
                )
            );
        }
        if ($this->getCountryCode() !== null) {
            $tagName = 'countryCode';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $address->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getCountryCode()
                )
            );
        }

        return $address;
    }

    /**
     * @param  \SimpleXMLElement $xml
     * @return Address
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        $address = new Address();

        if (isset($xml->streetName) && $xml->streetName != '') {
            $address->setStreetName((string) $xml->streetName);
        }
        if (isset($xml->number) && $xml->number != '') {
            $address->setNumber((string) $xml->number);
        }
        if (isset($xml->box) && $xml->box != '') {
            $address->setBox((string) $xml->box);
        }
        if (isset($xml->postalCode) && $xml->postalCode != '') {
            $address->setPostalCode((string) $xml->postalCode);
        }
        if (isset($xml->locality) && $xml->locality != '') {
            $address->setLocality((string) $xml->locality);
        }
        if (isset($xml->countryCode) && $xml->countryCode != '') {
            $address->setCountryCode((string) $xml->countryCode);
        }

        return $address;
    }
}
