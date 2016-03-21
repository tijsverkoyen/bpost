<?php
namespace Bpost\BpostApiClient\Bpost\Order\Box\National;

use Bpost\BpostApiClient\Common\ComplexAttribute;

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
     * @return ParcelLockerReducedMobilityZone|\Bpost\BpostApiClient\IComplexAttribute
     */
    static function createFromXml(\SimpleXMLElement $xml)
    {
        $self = new self();
        return $self;
    }
}
