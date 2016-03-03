<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box\National;

class ParcelLockerReducedMobilityZoneTest extends \PHPUnit_Framework_TestCase
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
        $xml = $expectedDocument->createElement('parcelLockerReducedMobilityZone');

        $expectedDocument->appendChild($xml);

        $actualDocument = self::createDomDocument();
        $self = new ParcelLockerReducedMobilityZone();
        $actualDocument->appendChild(
            $self->toXML($actualDocument)
        );

        $this->assertEquals($expectedDocument, $actualDocument);

        // With prefix

        $expectedDocument = self::createDomDocument();
        $xml = $expectedDocument->createElement('foo:parcelLockerReducedMobilityZone');
        $expectedDocument->appendChild($xml);

        $actualDocument = self::createDomDocument();
        $self = new ParcelLockerReducedMobilityZone();
        $actualDocument->appendChild(
            $self->toXML($actualDocument, 'foo')
        );

        $this->assertSame($expectedDocument->saveXML(), $actualDocument->saveXML());
    }

    public function testCreateFromXml()
    {
        $xml = new \SimpleXMLElement('<parcelLockerReducedMobilityZone/>');

        $this->assertInstanceOf(
            'TijsVerkoyen\Bpost\Bpost\Order\Box\National\ParcelLockerReducedMobilityZone',
            ParcelLockerReducedMobilityZone::createFromXml($xml)
        );
    }
}
