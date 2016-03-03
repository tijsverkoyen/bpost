<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box\National;

use TijsVerkoyen\Bpost\ComplexAttribute;

class ParcelLockerReducedMobilityZone extends ComplexAttribute
{

    /**
     * @param \DOMDocument $document
     * @param string       $prefix
     * @param string       $type
     * @return \DOMElement
     */
    function toXml(\DOMDocument $document, $prefix = null, $type = null)
    {
        $tagName = $this->getPrefixedTagName('parcelLockerReducedMobilityZone', $prefix);

        $xml = $document->createElement($tagName);

        return $xml;
    }

    /**
     * @todo Implement it, because today, nothing is specified
     * @param \SimpleXMLElement $xml
     * @return ParcelLockerReducedMobilityZone|\TijsVerkoyen\Bpost\IComplexAttribute
     */
    static function createFromXml(\SimpleXMLElement $xml)
    {
        $self = new self();
        return $self;
    }
}
