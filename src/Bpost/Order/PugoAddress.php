<?php
namespace Bpost\BpostApiClient\Bpost\Order;

/**
 * bPost PugoAddress class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class PugoAddress extends Address
{
    const TAG_NAME = 'pugoAddress';

    /**
     * @param \SimpleXMLElement $xml
     * @return PugoAddress
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        return parent::createFromXML($xml);
    }
}
