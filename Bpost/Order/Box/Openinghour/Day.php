<?php

namespace TijsVerkoyen\Bpost\Bpost\Order\Box\Openinghour;

use TijsVerkoyen\Bpost\Exception;

/**
 * bPost Day class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class Day
{
    /**
     * @var string
     */
    private $day;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $day
     */
    public function setDay($day)
    {
        if (!in_array($day, self::getPossibleDayValues())) {
            throw new Exception(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', self::getPossibleDayValues())
                )
            );
        }

        $this->day = $day;
    }

    /**
     * @return string
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @return array
     */
    public static function getPossibleDayValues()
    {
        return array(
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saterday',
            'Sunday'
        );
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
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
     * @param string $day
     * @param string $value
     */
    public function __construct($day, $value)
    {
        $this->setDay($day);
        $this->setValue($value);
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @param  \DomDocument $document
     * @param  string       $prefix
     * @return \DomElement
     */
    public function toXML(\DOMDocument $document, $prefix = null)
    {
        $tagName = $this->getDay();
        if ($prefix !== null) {
            $tagName = $prefix . ':' . $tagName;
        }

        return $document->createElement($tagName, $this->getValue());
    }
}
