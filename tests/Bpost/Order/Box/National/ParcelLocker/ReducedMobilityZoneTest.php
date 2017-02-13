<?php

namespace Bpost\BpostApiClient\Bpost\Order\Box\National\ParcelLocker;

class ReducedMobilityZoneTest extends \PHPUnit_Framework_TestCase
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
     * Tests CashOnDelivery->toXML
     */
    public function testToXML()
    {

        // Without prefix

        $expectedDocument = self::createDomDocument();
        $xml = $expectedDocument->createElement('reducedMobilityZone');

        $expectedDocument->appendChild($xml);

        $actualDocument = self::createDomDocument();
        $self = new ReducedMobilityZone();
        $actualDocument->appendChild(
            $self->toXML($actualDocument)
        );

        $this->assertEquals($expectedDocument, $actualDocument);

        // With prefix

        $expectedDocument = self::createDomDocument();
        $xml = $expectedDocument->createElement('foo:reducedMobilityZone');
        $expectedDocument->appendChild($xml);

        $actualDocument = self::createDomDocument();
        $self = new ReducedMobilityZone();
        $actualDocument->appendChild(
            $self->toXML($actualDocument, 'foo')
        );

        $this->assertSame($expectedDocument->saveXML(), $actualDocument->saveXML());
    }

    public function testCreateFromXml()
    {
        $xml = new \SimpleXMLElement('<reducedMobilityZone/>');

        $this->assertInstanceOf(
            'Bpost\BpostApiClient\Bpost\Order\Box\National\ParcelLocker\ReducedMobilityZone',
            ReducedMobilityZone::createFromXml($xml)
        );
    }
}
