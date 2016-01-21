<?php
namespace TijsVerkoyen\Bpost\Bpost\Order;

/**
 * bPost ParcelsDepotAddress class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class ParcelsDepotAddress extends Address
{
    const TAG_NAME = 'parcelsDepotAddress';

    /**
     * @param \SimpleXMLElement $xml
     * @return ParcelsDepotAddress
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        return parent::createFromXML($xml);
    }
}
