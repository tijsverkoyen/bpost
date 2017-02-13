<?php
namespace Bpost\BpostApiClient\Bpost\Order\Box\National\ParcelLocker;

use Bpost\BpostApiClient\Common\BasicAttribute\EmailAddressCharacteristic;
use Bpost\BpostApiClient\Common\BasicAttribute\Language;
use Bpost\BpostApiClient\Common\BasicAttribute\PhoneNumber;
use Bpost\BpostApiClient\Common\ComplexAttribute;

class Unregistered extends ComplexAttribute
{

    /** @var Language */
    private $language;

    /** @var PhoneNumber */
    private $mobilePhone;

    /** @var EmailAddressCharacteristic */
    private $emailAddress;

    /** @var ReducedMobilityZone */
    private $reducedMobilityZone;

    /**
     * @return bool
     */
    public function hasLanguage()
    {
        return $this->language !== null;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->hasLanguage() ? $this->language->getValue() : null;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = new Language($language);
    }

    /**
     * @return bool
     */
    public function hasMobilePhone()
    {
        return $this->mobilePhone !== null;
    }

    /**
     * @return string
     */
    public function getMobilePhone()
    {
        return $this->hasMobilePhone() ? $this->mobilePhone->getValue() : null;
    }

    /**
     * @param string $mobilePhone
     */
    public function setMobilePhone($mobilePhone)
    {
        $this->mobilePhone = new PhoneNumber($mobilePhone);
    }

    /**
     * @return bool
     */
    public function hasEmailAddress()
    {
        return $this->emailAddress !== null;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->hasEmailAddress() ? $this->emailAddress->getValue() : null;
    }

    /**
     * @param string $emailAddress
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = new EmailAddressCharacteristic($emailAddress);
    }

    /**
     * @return bool
     */
    public function hasReducedMobilityZone()
    {
        return $this->reducedMobilityZone !== null;
    }

    /**
     * @return ReducedMobilityZone
     */
    public function getReducedMobilityZone()
    {
        return $this->reducedMobilityZone;
    }

    /**
     * @param ReducedMobilityZone $reducedMobilityZone
     */
    public function setReducedMobilityZone(ReducedMobilityZone $reducedMobilityZone)
    {
        $this->reducedMobilityZone = $reducedMobilityZone;
    }

    /**
     * @param \DOMDocument $document
     * @param string       $prefix
     * @param string       $type
     * @return \DOMElement
     */
    function toXml(\DOMDocument $document, $prefix = null, $type = null)
    {
        $tagName = $this->getPrefixedTagName('unregistered', $prefix);

        $xml = $document->createElement($tagName);

        if ($this->hasLanguage()) {
            $tagName = $this->getPrefixedTagName('language', $prefix);
            $xml->appendChild($document->createElement($tagName, $this->getLanguage()));
        }

        if ($this->getMobilePhone() !== null) {
            $tagName = $this->getPrefixedTagName('mobilePhone', $prefix);
            $xml->appendChild($document->createElement($tagName, $this->getMobilePhone()));
        }

        if ($this->getEmailAddress() !== null) {
            $tagName = $this->getPrefixedTagName('emailAddress', $prefix);
            $xml->appendChild($document->createElement($tagName, $this->getEmailAddress()));
        }

        if ($this->hasReducedMobilityZone()) {
            $xml->appendChild(
                $this->getReducedMobilityZone()->toXML($document, $prefix, $type)
            );
        }

        return $xml;
    }

    /**
     * @param \SimpleXMLElement $xml
     *
     * @return Unregistered
     */
    static function createFromXml(\SimpleXMLElement $xml)
    {
        $self = new self();

        if (isset($xml->language) && $xml->language != '') {
            $self->setLanguage((string)$xml->language);
        }

        if (isset($xml->mobilePhone) && $xml->mobilePhone != '') {
            $self->setMobilePhone((string)$xml->mobilePhone);
        }

        if (isset($xml->emailAddress) && $xml->emailAddress != '') {
            $self->setEmailAddress((string)$xml->emailAddress);
        }

        if (isset($xml->parcelLockerReducedMobilityZone)) {
            $self->setReducedMobilityZone(
                ReducedMobilityZone::createFromXml($xml->reducedMobilityZone)
            );
        }

        return $self;
    }
}
