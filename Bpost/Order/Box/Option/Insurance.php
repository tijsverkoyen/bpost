<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box\Option;

use TijsVerkoyen\Bpost\Exception;

/**
 * bPost Insurance class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class Insurance extends Option
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $value;

    /**
     * @return array
     */
    public static function getPossibleTypeValues()
    {
        return array(
            'basicInsurance',
            'additionalInsurance',
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
     * @param string $value
     */
    public function setValue($value)
    {
        if (!in_array($value, self::getPossibleValueValues())) {
            throw new Exception(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', self::getPossibleValueValues())
                )
            );
        }

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public static function getPossibleValueValues()
    {
        return array(
            2,
            3,
            4,
            5,
            6,
            7,
            8,
            9,
            10,
            11
        );
    }

    /**
     * @param string      $type
     * @param string      $language
     * @param string|null $emailAddress
     * @param string|null $mobilePhone
     */
    public function __construct($type, $value = null)
    {
        $this->setType($type);
        if ($value !== null) {
            $this->setValue($value);
        }
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @return array
     */
    public function toXMLArray()
    {
        $data = array();
        $data[$this->getType()] = array();

        if ($this->getValue() !== null) {
            $data[$this->getType()]['@attributes']['value'] = $this->getValue();
        }

        return array('insured' => $data);
    }
}
