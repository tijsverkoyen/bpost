<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box\Option;

use TijsVerkoyen\Bpost\Exception;

/**
 * bPost Messaging class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class Messaging extends Option
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $language;

    /**
     * @var string
     */
    private $emailAddress;

    /**
     * @var string
     */
    private $mobilePhone;

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
     * @param string $language
     */
    public function setLanguage($language)
    {
        $language = strtoupper($language);

        if (!in_array($language, self::getPossibleLanguageValues())) {
            throw new Exception(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', self::getPossibleLanguageValues())
                )
            );
        }

        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return array
     */
    public static function getPossibleLanguageValues()
    {
        return array(
            'EN',
            'NL',
            'FR',
            'DE',
        );
    }

    /**
     * @param string $mobilePhone
     */
    public function setMobilePhone($mobilePhone)
    {
        $length = 20;
        if (mb_strlen($mobilePhone) > $length) {
            throw new Exception(sprintf('Invalid length, maximum is %1$s.', $length));
        }

        $this->mobilePhone = $mobilePhone;
    }

    /**
     * @return string
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * @return array
     */
    public static function getPossibleTypeValues()
    {
        return array(
            'infoDistributed',
            'infoNextDay',
            'infoReminder',
            'keepMeInformed',
        );
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {

        if (!in_array($type, self::getPossibleTypeValues())) {
            throw new Exception(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', self::getPossibleTypeValues())
                )
            );
        }

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string      $type
     * @param string      $language
     * @param string|null $emailAddress
     * @param string|null $mobilePhone
     */
    public function __construct($type, $language, $emailAddress = null, $mobilePhone = null)
    {
        $this->setType($type);
        $this->setLanguage($language);

        if ($emailAddress !== null) {
            $this->setEmailAddress($emailAddress);
        }
        if ($mobilePhone !== null) {
            $this->setMobilePhone($mobilePhone);
        }
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @param  \DomDocument $document
     * @param  string       $prefix
     * @return \DomElement
     */
    public function toXML(\DOMDocument $document, $prefix = 'common')
    {
        $tagName = $this->getType();
        if ($prefix !== null) {
            $tagName = $prefix . ':' . $tagName;
        }

        $messaging = $document->createElement($tagName);
        $messaging->setAttribute('language', $this->getLanguage());

        if ($this->getEmailAddress() !== null) {
            $tagName = 'emailAddress';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $messaging->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getEmailAddress()
                )
            );
        }
        if ($this->getMobilePhone() !== null) {
            $tagName = 'mobilePhone';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $messaging->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getMobilePhone()
                )
            );
        }

        return $messaging;
    }

    /**
     * @param  \SimpleXMLElement $xml
     * @return Messaging
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        $messaging = new Messaging(
            $xml->getName(), (string) $xml->attributes()->language
        );

        $data = $xml->{$xml->getName()};
        if (isset($data->emailAddress) && $data->emailAddress != '') {
            $messaging->setEmailAddress((string) $data->emailAddress);
        }
        if (isset($data->mobilePhone) && $data->mobilePhone != '') {
            $messaging->setMobilePhone((string) $data->mobilePhone);
        }

        return $messaging;
    }
}
