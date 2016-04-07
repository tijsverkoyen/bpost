<?php

namespace Bpost\BpostApiClient\Geo6;

use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostInvalidXmlResponseException;

/**
 * Geo6 class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class Poi
{
    /** @var string */
    private $id;

    /** @var string */
    private $type;

    /** @var string */
    private $office;

    /** @var string */
    private $street;

    /** @var string */
    private $nr;

    /** @var string */
    private $zip;

    /** @var string */
    private $city;

    /** @var int */
    private $x;

    /** @var int */
    private $y;

    /** @var float */
    private $latitude;

    /** @var float */
    private $longitude;

    /** @var array */
    private $services;

    /** @var array */
    private $hours;

    /** @var array */
    private $closedFrom;

    /** @var array */
    private $closedTo;

    /** @var string */
    private $note;

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = (string)$city;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param array $closedFrom
     */
    public function setClosedFrom(array $closedFrom)
    {
        $this->closedFrom = $closedFrom;
    }

    /**
     * @return array
     */
    public function getClosedFrom()
    {
        return $this->closedFrom;
    }

    /**
     * @param array $closedTo
     */
    public function setClosedTo(array $closedTo)
    {
        $this->closedTo = $closedTo;
    }

    /**
     * @return array
     */
    public function getClosedTo()
    {
        return $this->closedTo;
    }

    /**
     * @param int $index
     * @param Day $day
     */
    public function addHour($index, Day $day)
    {
        $this->hours[(int)$index] = $day;
    }

    /**
     * @param Day[] $hours
     */
    public function setHours(array $hours)
    {
        $this->hours = $hours;
    }

    /**
     * @return Day[]
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = (string)$id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = (float)$latitude;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = (float)$longitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = (string)$note;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $nr
     */
    public function setNr($nr)
    {
        $this->nr = (string)$nr;
    }

    /**
     * @return string
     */
    public function getNr()
    {
        return $this->nr;
    }

    /**
     * @param string $office
     */
    public function setOffice($office)
    {
        $this->office = (string)$office;
    }

    /**
     * @return string
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * @param Service $service
     */
    public function addService(Service $service)
    {
        $this->services[] = $service;
    }

    /**
     * @param Service[] $services
     */
    public function setServices(array $services)
    {
        $this->services = $services;
    }

    /**
     * @return Service[]
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = (string)$street;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = (string)$type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $x
     */
    public function setX($x)
    {
        $this->x = (int)$x;
    }

    /**
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @param int $y
     */
    public function setY($y)
    {
        $this->y = (int)$y;
    }

    /**
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @param string $zip
     */
    public function setZip($zip)
    {
        $this->zip = (string)$zip;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Create a POI based on an XML-object
     *
     * @param  \SimpleXMLElement $xml
     * @return Poi
     * @throws BpostInvalidXmlResponseException
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        if (!isset($xml->Record)) {
            throw new BpostInvalidXmlResponseException('"Record" missing');
        }

        $recordXml = $xml->Record;

        $poi = new Poi();

        if (isset($recordXml->Id) && $recordXml->Id != '') {
            $poi->setId((string)$recordXml->Id);
        }
        if (isset($recordXml->ID) && $recordXml->ID != '') {
            $poi->setId((string)$recordXml->ID);
        }
        if (isset($recordXml->Type) && $recordXml->Type != '') {
            $poi->setType((string)$recordXml->Type);
        }
        if (isset($recordXml->Name) && $recordXml->Name != '') {
            $poi->setOffice((string)$recordXml->Name);
        }
        if (isset($recordXml->OFFICE) && $recordXml->OFFICE != '') {
            $poi->setOffice((string)$recordXml->OFFICE);
        }
        if (isset($recordXml->Street) && $recordXml->Street != '') {
            $poi->setStreet((string)$recordXml->Street);
        }
        if (isset($recordXml->STREET) && $recordXml->STREET != '') {
            $poi->setStreet((string)$recordXml->STREET);
        }
        if (isset($recordXml->Number) && $recordXml->Number != '') {
            $poi->setNr((string)$recordXml->Number);
        }
        if (isset($recordXml->NR) && $recordXml->NR != '') {
            $poi->setNr((string)$recordXml->NR);
        }
        if (isset($recordXml->Zip) && $recordXml->Zip != '') {
            $poi->setZip((string)$recordXml->Zip);
        }
        if (isset($recordXml->ZIP) && $recordXml->ZIP != '') {
            $poi->setZip((string)$recordXml->ZIP);
        }
        if (isset($recordXml->City) && $recordXml->City != '') {
            $poi->setCity((string)$recordXml->City);
        }
        if (isset($recordXml->CITY) && $recordXml->CITY != '') {
            $poi->setCity((string)$recordXml->CITY);
        }
        if (isset($recordXml->X) && $recordXml->X != '') {
            $poi->setX((int)$recordXml->X);
        }
        if (isset($recordXml->Y) && $recordXml->Y != '') {
            $poi->setY((int)$recordXml->Y);
        }
        if (isset($recordXml->Longitude) && $recordXml->Longitude != '') {
            $poi->setLongitude((float)$recordXml->Longitude);
        }
        if (isset($recordXml->Latitude) && $recordXml->Latitude != '') {
            $poi->setLatitude((float)$recordXml->Latitude);
        }
        if (isset($recordXml->Services->Service)) {
            foreach ($recordXml->Services->Service as $service) {
                $poi->addService(Service::createFromXML($service));
            }
        }

        if (isset($recordXml->Hours->Monday)) {
            $poi->addHour(Day::DAY_INDEX_MONDAY, Day::createFromXML($recordXml->Hours->Monday));
        }
        if (isset($recordXml->Hours->Tuesday)) {
            $poi->addHour(Day::DAY_INDEX_TUESDAY, Day::createFromXML($recordXml->Hours->Tuesday));
        }
        if (isset($recordXml->Hours->Wednesday)) {
            $poi->addHour(Day::DAY_INDEX_WEDNESDAY, Day::createFromXML($recordXml->Hours->Wednesday));
        }
        if (isset($recordXml->Hours->Thursday)) {
            $poi->addHour(Day::DAY_INDEX_THURSDAY, Day::createFromXML($recordXml->Hours->Thursday));
        }
        if (isset($recordXml->Hours->Friday)) {
            $poi->addHour(Day::DAY_INDEX_FRIDAY, Day::createFromXML($recordXml->Hours->Friday));
        }
        if (isset($recordXml->Hours->Saturday)) {
            $poi->addHour(Day::DAY_INDEX_SATURDAY, Day::createFromXML($recordXml->Hours->Saturday));
        }
        if (isset($recordXml->Hours->Sunday)) {
            $poi->addHour(Day::DAY_INDEX_SUNDAY, Day::createFromXML($recordXml->Hours->Sunday));
        }
        if (isset($recordXml->ClosedFrom) && $recordXml->ClosedFrom != '') {
            $poi->setClosedFrom((string)$recordXml->ClosedFrom);
        }
        if (isset($recordXml->ClosedTo) && $recordXml->ClosedTo != '') {
            $poi->setClosedTo((string)$recordXml->ClosedTo);
        }
        if (isset($recordXml->NOTE) && $recordXml->NOTE != '') {
            $poi->setNote((string)$recordXml->NOTE);
        }

        return $poi;
    }
}
