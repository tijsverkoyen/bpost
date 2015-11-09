<?php
namespace Bpost;

use TijsVerkoyen\Bpost\Bpost\Order\ParcelsDepotAddress;

class ParcelsDepotAddressTest extends \PHPUnit_Framework_TestCase
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
     * Tests Address->toXML
     */
    public function testToXML()
    {
        $data = array(
            'streetName' => 'Afrikalaan',
            'number' => '2890',
            'box' => '3',
            'postalCode' => '9000',
            'locality' => 'Gent',
            'countryCode' => 'BE',
        );

        $expectedDocument = self::createDomDocument();
        $address = $expectedDocument->createElement('parcelsDepotAddress');
        foreach ($data as $key => $value) {
            $address->appendChild(
                $expectedDocument->createElement($key, $value)
            );
        }
        $expectedDocument->appendChild($address);

        $actualDocument = self::createDomDocument();
        $address = new ParcelsDepotAddress(
            $data['streetName'],
            $data['number'],
            $data['box'],
            $data['postalCode'],
            $data['locality'],
            $data['countryCode']
        );
        $actualDocument->appendChild(
            $address->toXML($actualDocument, null)
        );

        $this->assertEquals($expectedDocument, $actualDocument);
    }

    /**
     * Test validation in the setters
     */
    public function testFaultyProperties()
    {
        $address = new ParcelsDepotAddress();

        try {
            $address->setBox(str_repeat('a', 9));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertSame('Invalid length, maximum is 8.', $e->getMessage());
        }

        try {
            $address->setCountryCode(str_repeat('a', 3));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertSame('Invalid length, maximum is 2.', $e->getMessage());
        }

        try {
            $address->setLocality(str_repeat('a', 41));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertSame('Invalid length, maximum is 40.', $e->getMessage());
        }

        try {
            $address->setNumber(str_repeat('a', 9));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertSame('Invalid length, maximum is 8.', $e->getMessage());
        }

        try {
            $address->setPostalCode(str_repeat('a', 41));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertSame('Invalid length, maximum is 40.', $e->getMessage());
        }

        try {
            $address->setStreetName(str_repeat('a', 41));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertSame('Invalid length, maximum is 40.', $e->getMessage());
        }
    }
}
