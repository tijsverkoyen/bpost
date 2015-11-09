<?php
namespace Bpost;

use TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Signature;

class SignatureTest extends \PHPUnit_Framework_TestCase
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
     * Tests Signature->toXML
     */
    public function testToXML()
    {
        $expectedDocument = self::createDomDocument();
        $expectedDocument->appendChild(
            $expectedDocument->createElement('signed')
        );

        $actualDocument = self::createDomDocument();
        $signature = new Signature();
        $actualDocument->appendChild(
            $signature->toXML($actualDocument)
        );

        $this->assertEquals($expectedDocument, $actualDocument);

        $expectedDocument = self::createDomDocument();
        $expectedDocument->appendChild(
            $expectedDocument->createElement('foo:signed')
        );

        $actualDocument = self::createDomDocument();
        $signature = new Signature();
        $actualDocument->appendChild(
            $signature->toXML($actualDocument, 'foo')
        );

        $this->assertEquals($expectedDocument->saveXML(), $actualDocument->saveXML());
    }
}
