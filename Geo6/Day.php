<?php
/**
 * Created by PhpStorm.
 * User: tijs
 * Date: 10/04/14
 * Time: 16:42
 */

namespace TijsVerkoyen\Bpost\Geo6;


class Day
{
    /**
     * @var string
     */
    private $amOpen;

    /**
     * @var string
     */
    private $amClose;

    /**
     * @var string
     */
    private $pmOpen;

    /**
     * @var string
     */
    private $pmClose;

    /**
     * @var string
     */
    private $day;

    /**
     * @param string $amClose
     */
    public function setAmClose($amClose)
    {
        $this->amClose = $amClose;
    }

    /**
     * @return string
     */
    public function getAmClose()
    {
        return $this->amClose;
    }

    /**
     * @param string $amOpen
     */
    public function setAmOpen($amOpen)
    {
        $this->amOpen = $amOpen;
    }

    /**
     * @return string
     */
    public function getAmOpen()
    {
        return $this->amOpen;
    }

    /**
     * @param string $day
     */
    public function setDay($day)
    {
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
     * Get the index for a day
     *
     * @return int
     */
    public function getDayIndex()
    {
        switch (strtolower($this->getDay())) {
            case 'monday':
                return 1;
            case 'tuesday':
                return 2;
            case 'wednesday':
                return 3;
            case 'thursday':
                return 4;
            case 'friday':
                return 5;
            case 'saturday':
                return 6;
            case 'sunday':
                return 7;
        }
    }

    /**
     * @param string $pmClose
     */
    public function setPmClose($pmClose)
    {
        $this->pmClose = $pmClose;
    }

    /**
     * @return string
     */
    public function getPmClose()
    {
        return $this->pmClose;
    }

    /**
     * @param string $pmOpen
     */
    public function setPmOpen($pmOpen)
    {
        $this->pmOpen = $pmOpen;
    }

    /**
     * @return string
     */
    public function getPmOpen()
    {
        return $this->pmOpen;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return Day
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        $day = new Day();
        $day->setDay($xml->getName());

        if (isset($xml->AMOpen) && $xml->AMOpen != '') {
            $day->setAmOpen((string) $xml->AMOpen);
        }
        if (isset($xml->AMClose) && $xml->AMClose != '') {
            $day->setAmClose((string) $xml->AMClose);
        }
        if (isset($xml->PMOpen) && $xml->PMOpen != '') {
            $day->setPmOpen((string) $xml->PMOpen);
        }
        if (isset($xml->PMClose) && $xml->PMClose != '') {
            $day->setPmClose((string) $xml->PMClose);
        }

        return $day;
    }
}
