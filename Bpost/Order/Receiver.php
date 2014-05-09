<?php
namespace TijsVerkoyen\Bpost\Bpost\Order;

/**
 * bPost Receiver class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Receiver extends Customer
{
    const TAG_NAME = 'receiver';

    /**
     * @param  \SimpleXMLElement $xml
     * @return Receiver
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        $receiver = new Receiver();
        $receiver = parent::createFromXMLHelper($xml, $receiver);

        return $receiver;
    }
}
