<?php

namespace Bpost\BpostApiClient\Bpost\Order\Box\OpeningHour;

use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;

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
    const DAY_MONDAY = 'Monday';
    const DAY_TUESDAY = 'Tuesday';
    const DAY_WEDNESDAY = 'Wednesday';
    const DAY_THURSDAY = 'Thursday';
    const DAY_FRIDAY = 'Friday';
    const DAY_SATURDAY = 'Saturday';
    const DAY_SUNDAY = 'Sunday';

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
     * @throws BpostInvalidValueException
     */
    public function setDay($day)
    {
        if (!in_array($day, self::getPossibleDayValues())) {
            throw new BpostInvalidValueException('day', $day, self::getPossibleDayValues());
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
            self::DAY_MONDAY,
            self::DAY_TUESDAY,
            self::DAY_WEDNESDAY,
            self::DAY_THURSDAY,
            self::DAY_FRIDAY,
            self::DAY_SATURDAY,
            self::DAY_SUNDAY,
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
     *
     * @throws BpostInvalidValueException
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
