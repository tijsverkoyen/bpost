<?php
namespace TijsVerkoyen\Bpost;

/**
 * bPost Notification class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Notification
{
    /**
     * Generic info
     *
     * @var string
     */
    private $emailAddress, $mobilePhone, $fixedPhone, $language;

    /**
     * Create a notification
     *
     * @param string           $language
     * @param string[otpional] $emailAddress
     * @param string[otpional] $mobilePhone
     * @param string[otpional] $fixedPhone
     */
    public function __construct($language, $emailAddress = null, $mobilePhone = null, $fixedPhone = null)
    {
        if(
            $emailAddress !== null && $mobilePhone !== null ||
            $emailAddress !== null && $fixedPhone !== null ||
            $mobilePhone !== null && $fixedPhone !== null ||
            $fixedPhone !== null && $mobilePhone !== null
        )
        {
            throw new Exception('You can\'t specify multiple notifications.');
        }

        $this->setLanguage($language);
        if($emailAddress !== null) $this->setEmailAddress($emailAddress);
        if($mobilePhone !== null) $this->setMobilePhone($mobilePhone);
        if($fixedPhone !== null) $this->setFixedPhone($fixedPhone);
    }

    /**
     * Get the email address
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Get the fixed phone
     *
     * @return string
     */
    public function getFixedPhone()
    {
        return $this->fixedPhone;
    }

    /**
     * Get the language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Get the mobile phone
     *
     * @return string
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * Set the email address
     *
     * @param string $emailAddress
     */
    public function setEmailAddress($emailAddress)
    {
        if(mb_strlen($emailAddress) > 50) throw new Exception('Invalid length for emailAddress, maximum is 50.');
        $this->emailAddress = $emailAddress;
    }

    /**
     * Set the fixed phone
     *
     * @param string $fixedPhone
     */
    public function setFixedPhone($fixedPhone)
    {
        if(mb_strlen($fixedPhone) > 20) throw new Exception('Invalid length for fixedPhone, maximum is 20.');
        $this->fixedPhone = $fixedPhone;
    }

    /**
     * Set the language
     *
     * @param string $language Allowed values are EN, NL, FR, DE.
     */
    public function setLanguage($language)
    {
        $allowedLanguages = array('EN', 'NL', 'FR', 'DE');

        // validate
        if (!in_array($language, $allowedLanguages)) {
            throw new Exception(
                'Invalid value for language (' . $language . '), allowed values are: ' .
                implode(',  ', $allowedLanguages) . '.'
            );
        }
        $this->language = $language;
    }

    /**
     * Set the mobile phone
     *
     * @param string $mobilePhone
     */
    public function setMobilePhone($mobilePhone)
    {
        if(mb_strlen($mobilePhone) > 20) throw new Exception('Invalid length for mobilePhone, maximum is 20.');
        $this->mobilePhone = $mobilePhone;
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @return array
     */
    public function toXMLArray()
    {
        $data = array();
        $data['@attributes']['language'] = $this->language;

        if(isset($this->emailAddress)) $data['emailAddress'] = $this->emailAddress;
        if(isset($this->mobilePhone)) $data['mobilePhone'] = $this->mobilePhone;
        if(isset($this->fixedPhone)) $data['fixedPhone'] = $this->fixedPhone;

        return $data;
    }
}

