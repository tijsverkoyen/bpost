<?php
namespace Bpost;

require_once __DIR__ . '/../../../../../autoload.php';

use TijsVerkoyen\Bpost\Bpost\Order\Customer;
use TijsVerkoyen\Bpost\Bpost\Order\Address;

class CustomerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Customer->toXMLArray
     */
    public function testToXMLArray()
    {
        $data = array(
            'name' => 'Tijs Verkoyen',
            'company' => 'Sumo Coders',
            'address' => array(
                'streetName' => 'Afrikalaan',
                'number' => '289',
                'box' => '3',
                'postalCode' => '9000',
                'locality' => 'Gent',
                'countryCode' => 'BE',
            ),
            'emailAddress' => 'bpost@verkoyen.eu',
            'phoneNumber' => '+32 9 395 02 51',
        );

        $address = new Address(
            $data['address']['streetName'],
            $data['address']['number'],
            $data['address']['box'],
            $data['address']['postalCode'],
            $data['address']['locality'],
            $data['address']['countryCode']
        );

        $sender = new Customer();
        $sender->setName($data['name']);
        $sender->setCompany($data['company']);
        $sender->setAddress($address);
        $sender->setEmailAddress($data['emailAddress']);
        $sender->setPhoneNumber($data['phoneNumber']);

        $xmlArray = $sender->toXMLArray();

        $this->assertEquals($data, $xmlArray);
    }

    /**
     * Test validation in the setters
     */
    public function testFaultyProperties()
    {
        $sender = new Customer();

        try {
            $sender->setEmailAddress(str_repeat('a', 51));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals('Invalid length, maximum is 50.', $e->getMessage());
        }

        try {
            $sender->setPhoneNumber(str_repeat('a', 21));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals('Invalid length, maximum is 20.', $e->getMessage());
        }
    }
}
