<?php
namespace Bpost;

require_once __DIR__ . '/../../../../../autoload.php';

use TijsVerkoyen\Bpost\Bpost\Order\Address;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Address->toXMLArray
     */
    public function testToXMLArray()
    {
        $data = array(
            'streetName' => 'Afrikalaan',
            'number' => '2890',
            'box' => '3',
            'postalCode' => '9000',
            'locality' => 'Gent',
            'countryCode' => 'BE',
        );

        $address = new Address(
            $data['streetName'],
            $data['number'],
            $data['box'],
            $data['postalCode'],
            $data['locality'],
            $data['countryCode']
        );

        $xmlArray = $address->toXMLArray();

        $this->assertEquals($data, $xmlArray);
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
}
