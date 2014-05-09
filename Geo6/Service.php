<?php

namespace TijsVerkoyen\Bpost\Geo6;

/**
 * Geo6 class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
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
     * @param  \SimpleXMLElement $xml
     * @return Service
     */
    public static function createFromXML(\SimpleXMLElement $xml)
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
