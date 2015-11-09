<?php
namespace Bpost;

use TijsVerkoyen\Bpost\Bpost\Order\Customer;
use TijsVerkoyen\Bpost\Bpost\Order\Address;

class CustomerTest extends \PHPUnit_Framework_TestCase
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
     * Tests Customer->toXML
     */
    public function testToXML()
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

        $expectedDocument = self::createDomDocument();
        $sender = $expectedDocument->createElement('customer');
        foreach ($data as $key => $value) {
            $key = 'common:' . $key;
            if ($key == 'common:address') {
                $address = $expectedDocument->createElement($key);
                foreach ($value as $key2 => $value2) {
                    $key2 = 'common:' . $key2;
                    $address->appendChild(
                        $expectedDocument->createElement($key2, $value2)
                    );
                }
                $sender->appendChild($address);
            } else {
                $sender->appendChild(
                    $expectedDocument->createElement($key, $value)
                );
            }
        }
        $expectedDocument->appendChild($sender);

        $actualDocument = self::createDomDocument();
        $address = new Address(
            $data['address']['streetName'],
            $data['address']['number'],
            $data['address']['box'],
            $data['address']['postalCode'],
            $data['address']['locality'],
            $data['address']['countryCode']
        );
        $customer = new Customer();
        $customer->setName($data['name']);
        $customer->setCompany($data['company']);
        $customer->setAddress($address);
        $customer->setEmailAddress($data['emailAddress']);
        $customer->setPhoneNumber($data['phoneNumber']);
        $actualDocument->appendChild(
            $customer->toXML($actualDocument, null)
        );

        $this->assertSame($expectedDocument->saveXML(), $actualDocument->saveXML());
    }

    /**
     * Test validation in the setters
     */
    public function testFaultyProperties()
    {
        $customer = new Customer();

        try {
            $customer->setEmailAddress(str_repeat('a', 51));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertSame('Invalid length, maximum is 50.', $e->getMessage());
        }

        try {
            $customer->setPhoneNumber(str_repeat('a', 21));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertSame('Invalid length, maximum is 20.', $e->getMessage());
        }
    }
}
