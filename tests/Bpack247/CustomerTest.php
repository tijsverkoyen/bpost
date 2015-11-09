<?php
namespace Bpost\Bpack;

use TijsVerkoyen\Bpost\Bpack247\Customer;
use TijsVerkoyen\Bpost\Bpack247\CustomerPackStation;

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
            'FirstName' => 'Tijs',
            'LastName' => 'Verkoyen',
            'Email' => 'bpost@verkoyen.eu',
            'Street' => 'Afrikalaan',
            'Number' => '289',
            'MobilePrefix' => '0032',
            'MobileNumber' => '486123456',
            'PostalCode' => '9000',
            'PreferredLanguage' => 'nl-BE',
            'Title' => 'Mr.',
        );

        $expectedDocument = self::createDomDocument();
        $customer = $expectedDocument->createElement(
            'Customer'
        );
        $customer->setAttribute(
            'xmlns',
            'http://schema.post.be/ServiceController/customer'
        );
        $customer->setAttribute(
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance'
        );
        $customer->setAttribute(
            'xsi:schemaLocation',
            'http://schema.post.be/ServiceController/customer'
        );
        $customer->appendChild(
            $expectedDocument->createElement('FirstName', $data['FirstName'])
        );
        $customer->appendChild(
            $expectedDocument->createElement('LastName', $data['LastName'])
        );
        $customer->appendChild(
            $expectedDocument->createElement('Street', $data['Street'])
        );
        $customer->appendChild(
            $expectedDocument->createElement('Number', $data['Number'])
        );
        $customer->appendChild(
            $expectedDocument->createElement('Email', $data['Email'])
        );
        $customer->appendChild(
            $expectedDocument->createElement('MobilePrefix', $data['MobilePrefix'])
        );
        $customer->appendChild(
            $expectedDocument->createElement('MobileNumber', $data['MobileNumber'])
        );
        $customer->appendChild(
            $expectedDocument->createElement('PostalCode', $data['PostalCode'])
        );
        $customer->appendChild(
            $expectedDocument->createElement('PreferredLanguage', $data['PreferredLanguage'])
        );
        $customer->appendChild(
            $expectedDocument->createElement('Title', $data['Title'])
        );
        $expectedDocument->appendChild($customer);

        $customer = new Customer();
        $customer->setFirstName($data['FirstName']);
        $customer->setLastName($data['LastName']);
        $customer->setEmail($data['Email']);
        $customer->setStreet($data['Street']);
        $customer->setNumber($data['Number']);
        $customer->setMobileNumber($data['MobileNumber']);
        $customer->setPostalCode($data['PostalCode']);
        $customer->setPreferredLanguage($data['PreferredLanguage']);
        $customer->setTitle($data['Title']);

        $actualDocument = self::createDomDocument();
        $actualDocument->appendChild(
            $customer->toXML($actualDocument)
        );

        $this->assertSame($expectedDocument->saveXML(), $actualDocument->saveXML());
    }

    /**
     * Tests Customer->toXML
     */
    public function testCreateFromXML()
    {
        $data = array(
            'UserID' => '5f1f1b07-a8c4-4d4c-bd5b-cdace6cb7c84',
            'FirstName' => 'Bruno',
            'LastName' => 'Vandenabeele',
            'Street' => 'Oplintersesteenweg',
            'Number' => '629',
            'CompanyName' => 'Bpost',
            'Country' => 'BE',
            'DateOfBirth' => '1974-07-02',
            'DeliveryCode' => '344337728',
            'Email' => 'bruno.vandenabeele@telenet.be',
            'MobilePrefix' => '0032',
            'MobileNumber' => '475813445',
            'Postalcode' => '3300',
            'PreferredLanguage' => 'nl-BE',
            'ReceivePromotions' => true,
            'actived' => true,
            'Title' => 'Mr',
            'Town' => 'Tienen',
            'PackStations' => array(
                array(
                    'OrderNumber' => '1',
                    'PackStationId' => '14472',
                ),
            )
        );

        $document = self::createDomDocument();
        $customerElement = $document->createElement('Customer');
        foreach ($data as $key => $value) {
            if ($key == 'PackStations') {
                continue;
            }

            $customerElement->appendChild(
                $document->createElement($key, $value)
            );
        }
        $customerPackStation = $document->createElement('CustomerPackStation');
        $customerPackStation->appendChild(
            $document->createElement('OrderNumber', 1)
        );
        $customerPackStation->appendChild(
            $document->createElement('PackstationID', 14472)
        );
        $packStations = $document->createElement('PackStations');
        $packStations->appendChild($customerPackStation);
        $customerElement->appendChild($packStations);
        $document->appendChild($customerElement);

        $customer = Customer::createFromXML(
            simplexml_load_string(
                $document->saveXML()
            )
        );

        $this->assertSame($data['UserID'], $customer->getUserID());
        $this->assertSame($data['FirstName'], $customer->getFirstName());
        $this->assertSame($data['LastName'], $customer->getLastName());
        $this->assertSame($data['Street'], $customer->getStreet());
        $this->assertSame($data['Number'], $customer->getNumber());
        $this->assertSame($data['CompanyName'], $customer->getCompanyName());
        $this->assertEquals(new \DateTime($data['DateOfBirth']), $customer->getDateOfBirth());
        $this->assertSame($data['DeliveryCode'], $customer->getDeliveryCode());
        $this->assertSame($data['Email'], $customer->getEmail());
        $this->assertSame($data['MobilePrefix'], $customer->getMobilePrefix());
        $this->assertSame($data['MobileNumber'], $customer->getMobileNumber());
        $this->assertSame($data['Postalcode'], $customer->getPostalCode());
        $this->assertSame($data['PreferredLanguage'], $customer->getPreferredLanguage());
        $this->assertSame($data['ReceivePromotions'], $customer->getReceivePromotions());
        $this->assertSame($data['actived'], $customer->getActivated());
        $this->assertSame($data['Title'] . '.', $customer->getTitle());
        $this->assertSame($data['Town'], $customer->getTown());
        $packStations = $customer->getPackStations();
        $this->assertSame($data['PackStations'][0]['OrderNumber'], $packStations[0]->getOrderNumber());
        $this->assertSame($data['PackStations'][0]['PackStationId'], $packStations[0]->getPackStationId());

        try {
            $xml = simplexml_load_string(
                '<Customer>
                </Customer>'
            );
            $customer = Customer::createFromXML($xml);
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertSame('No UserId found.', $e->getMessage());
        }
    }

    /**
     * Test validation in the setters
     */
    public function testFaultyProperties()
    {
        $customer = new Customer();

        try {
            $customer->setPreferredLanguage(str_repeat('a', 10));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertSame(
                'Invalid value, possible values are: ' . implode(
                    ', ',
                    Customer::getPossiblePreferredLanguageValues()
                ) . '.',
                $e->getMessage()
            );
        }
        try {
            $customer->setTitle(str_repeat('a', 10));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertSame(
                'Invalid value, possible values are: ' . implode(', ', Customer::getPossibleTitleValues()) . '.',
                $e->getMessage()
            );
        }
    }

    /**
     * Test some missing stuff to get full coverage
     */
    public function testMissingProperties()
    {
        $data = array(
            'IsComfortZoneUser' => true,
            'OptIn' => true,
            'UseInformationForThirdParty' => false,
            'UserName' => 'UserName',
            'PackStations' => array(
                'Foo',
                'Bar'
            )
        );

        $customer = new Customer();

        $customer->setIsComfortZoneUser($data['IsComfortZoneUser']);
        $this->assertSame($data['IsComfortZoneUser'], $customer->getIsComfortZoneUser());
        $customer->setOptIn($data['OptIn']);
        $this->assertSame($data['OptIn'], $customer->getOptIn());
        $customer->setUseInformationForThirdParty($data['UseInformationForThirdParty']);
        $this->assertSame($data['UseInformationForThirdParty'], $customer->getUseInformationForThirdParty());
        $customer->setUserName($data['UserName']);
        $this->assertSame($data['UserName'], $customer->getUserName());
        $customer->setPackStations($data['PackStations']);
        $this->assertSame($data['PackStations'], $customer->getPackStations());
    }
}
