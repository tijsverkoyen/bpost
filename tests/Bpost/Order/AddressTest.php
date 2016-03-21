<?php
namespace Bpost;

use Bpost\BpostApiClient\Bpost\Order\Address;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;

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

        $this->assertSame($expectedDocument->saveXML(), $actualDocument->saveXML());
    }

    /**
     * Test validation in the setters
     */
    public function testFaultyProperties()
    {
        $address = new Address();

        try {
            $address->setBox(str_repeat('a', 9));
            $this->fail('BpostInvalidLengthException not launched');
        } catch (BpostInvalidLengthException $e) {
            // Nothing, the exception is good
        } catch (\Exception $e) {
            $this->fail('BpostInvalidLengthException not caught');
        }

        try {
            $address->setCountryCode(str_repeat('a', 3));
            $this->fail('BpostInvalidLengthException not launched');
        } catch (BpostInvalidLengthException $e) {
            // Nothing, the exception is good
        } catch (\Exception $e) {
            $this->fail('BpostInvalidLengthException not caught');
        }

        try {
            $address->setLocality(str_repeat('a', 41));
            $this->fail('BpostInvalidLengthException not launched');
        } catch (BpostInvalidLengthException $e) {
            // Nothing, the exception is good
        } catch (\Exception $e) {
            $this->fail('BpostInvalidLengthException not caught');
        }

        try {
            $address->setNumber(str_repeat('a', 9));
            $this->fail('BpostInvalidLengthException not launched');
        } catch (BpostInvalidLengthException $e) {
            // Nothing, the exception is good
        } catch (\Exception $e) {
            $this->fail('BpostInvalidLengthException not caught');
        }

        try {
            $address->setPostalCode(str_repeat('a', 41));
            $this->fail('BpostInvalidLengthException not launched');
        } catch (BpostInvalidLengthException $e) {
            // Nothing, the exception is good
        } catch (\Exception $e) {
            $this->fail('BpostInvalidLengthException not caught');
        }

        try {
            $address->setStreetName(str_repeat('a', 41));
            $this->fail('BpostInvalidLengthException not launched');
        } catch (BpostInvalidLengthException $e) {
            // Nothing, the exception is good
        } catch (\Exception $e) {
            $this->fail('BpostInvalidLengthException not caught');
        }

        // Exceptions were caught,
        $this->assertTrue(true);
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

        $this->assertSame($data['streetName'], $address->getStreetName());
        $this->assertSame($data['number'], $address->getNumber());
        $this->assertSame($data['box'], $address->getBox());
        $this->assertSame($data['postalCode'], $address->getPostalCode());
        $this->assertSame($data['locality'], $address->getLocality());
        $this->assertSame($data['countryCode'], $address->getCountryCode());
    }
}
