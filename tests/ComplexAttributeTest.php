<?php

namespace TijsVerkoyen\Bpost;

use TijsVerkoyen\Bpost\Exception\BpostLogicException;
use TijsVerkoyen\Bpost\Exception\BpostLogicException\BpostInvalidLengthException;
use TijsVerkoyen\Bpost\Exception\BpostLogicException\BpostInvalidPatternException;
use TijsVerkoyen\Bpost\Exception\BpostLogicException\BpostInvalidValueException;

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
