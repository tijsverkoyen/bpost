<?php
/**
 * Created by PhpStorm.
 * User: tijs
 * Date: 10/04/14
 * Time: 14:10
 */

namespace TijsVerkoyen\Bpost\Geo6;


class Poi
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $office;

    /**
     * @var string
     */
    private $street;

    /**
     * @var string
     */
    private $nr;

    /**
     * @var string
     */
    private $zip;

    /**
     * @var string
     */
    private $city;

    /**
     * @var int
     */
    private $x;

    /**
     * @var int
     */
    private $y;

    /**
     * @var float
     */
    private $latitude;

    /**
     * @var float
     */
    private $longitude;

    /**
     * @var array
     */
    private $services;

    /**
     * @var array
     */
    private $hours;

    /**
     * @var array
     */
    private $closedFrom;

    /**
     * @var array
     */
    private $closedTo;

    /**
     * @var string
     */
    private $note;

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
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
    public function setClosedFrom($closedFrom)
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
    public function setClosedTo($closedTo)
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
     * @param array $hours
     */
    public function setHours($hours)
    {
        $this->hours = $hours;
    }

    /**
     * @return array
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
        $this->id = $id;
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
        $this->latitude = $latitude;
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
        $this->longitude = $longitude;
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
        $this->note = $note;
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
        $this->nr = $nr;
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
        $this->office = $office;
    }

    /**
     * @return string
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * @param array $services
     */
    public function setServices($services)
    {
        $this->services = $services;
    }

    /**
     * @return array
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
        $this->street = $street;
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
     * @param int $x
     */
    public function setX($x)
    {
        $this->x = $x;
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
        $this->y = $y;
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
        $this->zip = $zip;
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
     * @param SimpleXMLElement $xml
     * @return Poi
     */
    public static function createFromXML($xml)
    {
        $poi = new Poi();

        if (isset($xml->Id)) {
            $poi->setId((string) $xml->Id);
        }
        if (isset($xml->Type)) {
            $poi->setType((string) $xml->Type);
        }
        if (isset($xml->Name)) {
            $poi->setOffice((string) $xml->Name);
        }
        if (isset($xml->Street)) {
            $poi->setStreet((string) $xml->Street);
        }
        if (isset($xml->Number)) {
            $poi->setNr((string) $xml->Number);
        }
        if (isset($xml->Zip)) {
            $poi->setZip((string) $xml->Zip);
        }
        if (isset($xml->City)) {
            $poi->setCity((string) $xml->City);
        }
        if (isset($xml->X)) {
            $poi->setX((int) $xml->X);
        }
        if (isset($xml->Y)) {
            $poi->setY((int) $xml->Y);
        }
        if (isset($xml->Longitude)) {
            $poi->setLongitude((float) $xml->Longitude);
        }
        if (isset($xml->Latitude)) {
            $poi->setLatitude((float) $xml->Latitude);
        }

        return $poi;
    }
}
