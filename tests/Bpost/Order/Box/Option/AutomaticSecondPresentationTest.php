<?php
namespace Bpost;

use TijsVerkoyen\Bpost\Bpost\Order\Box\Option\AutomaticSecondPresentation;

class AutomaticSecondPresentationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create a generic DOM Document
     *
     * @return \DOMDocument
     */
    private static function createDomDocument()
    {
        $document = new \DOMDocument('1.0', 'utf-8');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;

        return $document;
    }

    /**
     * Tests AutomaticSecondPresentation->toXML
     */
    public function testToXML()
    {
        $expectedDocument = self::createDomDocument();
        $expectedDocument->appendChild(
            $expectedDocument->createElement('automaticSecondPresentation')
        );

        $actualDocument = self::createDomDocument();
        $automaticSecondPresentation = new AutomaticSecondPresentation();
        $actualDocument->appendChild(
            $automaticSecondPresentation->toXML($actualDocument)
        );

        $this->assertEquals($expectedDocument, $actualDocument);

        $expectedDocument = self::createDomDocument();
        $expectedDocument->appendChild(
            $expectedDocument->createElement('foo:automaticSecondPresentation')
        );

        $actualDocument = self::createDomDocument();
        $automaticSecondPresentation = new AutomaticSecondPresentation();
        $actualDocument->appendChild(
            $automaticSecondPresentation->toXML($actualDocument, 'foo')
        );

        $this->assertSame($expectedDocument->saveXML(), $actualDocument->saveXML());
    }
}
