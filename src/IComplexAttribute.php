<?php

namespace TijsVerkoyen\Bpost;

interface IComplexAttribute
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
     * @return IComplexAttribute
     */
    static function createFromXml(\SimpleXMLElement $xml);
}
