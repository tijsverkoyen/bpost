<?php

namespace TijsVerkoyen\Bpost\Common;

interface IAttribute
{
    /**
     * @param \DOMDocument $document
     * @param string       $prefix
     * @param string       $type
     * @return \DOMElement
     */
    function toXml(\DOMDocument $document, $prefix = null, $type = null);

    /**
     * @param \SimpleXMLElement $xml
     * @return IAttribute
     */
    static function createFromXml(\SimpleXMLElement $xml);
}
