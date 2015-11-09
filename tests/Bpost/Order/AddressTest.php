<?php
namespace Bpost;

use TijsVerkoyen\Bpost\Bpost\Order\Address;

class AddressTest extends \PHPUnit_Framework_TestCase
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
        $address = $expectedDocument->createElement('common:address');
        foreach ($data as $key => $value) {
            $address->appendChild(
                $expectedDocument->createElement($key, $value)
            );
        }
        $expectedDocument->appendChild($address);

        $actualDocument = self::createDomDocument();
        $address = new Address(
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

        $this->assertEquals($expectedDocument->saveXML(), $actualDocument->saveXML());
    }

    /**
     * Test validation in the setters
     */
    public function testFaultyProperties()
    {
        $address = new Address();

        try {
            $address->setBox(str_repeat('a', 9));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals('Invalid length, maximum is 8.', $e->getMessage());
        }

        try {
            $address->setCountryCode(str_repeat('a', 3));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals('Invalid length, maximum is 2.', $e->getMessage());
        }

        try {
            $address->setLocality(str_repeat('a', 41));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals('Invalid length, maximum is 40.', $e->getMessage());
        }

        try {
            $address->setNumber(str_repeat('a', 9));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals('Invalid length, maximum is 8.', $e->getMessage());
        }

        try {
            $address->setPostalCode(str_repeat('a', 41));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals('Invalid length, maximum is 40.', $e->getMessage());
        }

        try {
            $address->setStreetName(str_repeat('a', 41));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals('Invalid length, maximum is 40.', $e->getMessage());
        }
    }

    /**
     * Tests Address->createFromXML
     */
    public function testCreateFromXML()
    {
        $data = array(
            'streetName' => 'Afrikalaan',
            'number' => '289',
            'box' => '3',
            'postalCode' => '9000',
            'locality' => 'Gent',
            'countryCode' => 'BE',
        );

        $document = self::createDomDocument();
        $addressElement = $document->createElement('address');
        foreach ($data as $key => $value) {
            $addressElement->appendChild(
                $document->createElement($key, $value)
            );
        }
        $document->appendChild($addressElement);

        $address = Address::createFromXML(
            simplexml_load_string(
                $document->saveXML()
            )
        );

        $this->assertEquals($data['streetName'], $address->getStreetName());
        $this->assertEquals($data['number'], $address->getNumber());
        $this->assertEquals($data['box'], $address->getBox());
        $this->assertEquals($data['postalCode'], $address->getPostalCode());
        $this->assertEquals($data['locality'], $address->getLocality());
        $this->assertEquals($data['countryCode'], $address->getCountryCode());
    }
}
