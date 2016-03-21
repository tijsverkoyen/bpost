<?php

namespace Bpost\BpostApiClient;

use Bpost\BpostApiClient\Common\ComplexAttribute;
use Bpost\BpostApiClient\Exception\BpostLogicException;

class ComplexAttributeFake extends ComplexAttribute
{
    /**
     * @param \DOMDocument $document
     * @param string       $prefix
     * @param string       $type
     * @return \DOMElement
     */
    function toXml(\DOMDocument $document, $prefix = null, $type = null)
    {
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return IComplexAttribute
     */
    static function createFromXml(\SimpleXMLElement $xml)
    {
    }
}

class ComplexAttributeTest extends \PHPUnit_Framework_TestCase
{

    public function testGetPrefixedTagName()
    {
        $fake = new ComplexAttributeFake();
        $this->assertSame('fake:name', $fake->getPrefixedTagName('name', 'fake'));
        $this->assertSame('name', $fake->getPrefixedTagName('name', ''));
        $this->assertSame('name', $fake->getPrefixedTagName('name'));
    }

}
