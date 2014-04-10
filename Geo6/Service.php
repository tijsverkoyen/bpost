<?php
/**
 * Created by PhpStorm.
 * User: tijs
 * Date: 10/04/14
 * Time: 16:02
 */

namespace TijsVerkoyen\Bpost\Geo6;


class Service
{
    /**
     * @var string
     */
    private $category;

    /**
     * @var string
     */
    private $flag;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $flag
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;
    }

    /**
     * @return string
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param SimpleXMLElement $xml
     * @return Service
     */
    public static function createFromXML($xml)
    {
        $service = new Service();
        $service->setName((string) $xml);

        if (isset($xml['category'])) {
            $service->setCategory((string) $xml['category']);
        }
        if (isset($xml['flag'])) {
            $service->setFlag((string) $xml['flag']);
        }

        return $service;
    }
}
