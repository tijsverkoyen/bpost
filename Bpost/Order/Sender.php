<?php
namespace TijsVerkoyen\Bpost\Bpost\Order;

/**
 * bPost Sender class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Sender extends Customer
{
    const TAG_NAME = 'sender';

    /**
     * @param  \SimpleXMLElement $xml
     * @return Sender
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        $sender = new Sender();
        $sender = parent::createFromXMLHelper($xml, $sender);

        return $sender;
    }
}
