<?php
namespace Bpost;

use TijsVerkoyen\Bpost\Bpost\Order\Address;
use TijsVerkoyen\Bpost\Bpost\Order\Box\AtHome;
use TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Messaging;
use TijsVerkoyen\Bpost\Bpost\Order\Receiver;
use TijsVerkoyen\Bpost\Bpost\Order\Box\Openinghour\Day as OpeninghourDay;

class AtHomeTest extends \PHPUnit_Framework_TestCase
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
            'atHome' => array(
                'product' => 'bpack 24h Pro',
                'options' => array(
                    array(
                        'common:infoNextDay' => array(
                            '@attributes' => array(
                                'language' => 'NL',
                            ),
                            'common:emailAddress' => 'bpost@verkoyen.eu',
                        )
                    )
                ),
                'weight' => 2000,
                'openingHours' => array(
                    'Monday' => '10:00-17:00',
                ),
                'desiredDeliveryPlace' => 1234,
                'receiver' => array(
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
                ),
            ),
        );

        $expectedDocument = self::createDomDocument();
        $nationalBox = $expectedDocument->createElement('nationalBox');
        $expectedDocument->appendChild($nationalBox);
        $atHome = $expectedDocument->createElement('atHome');
        $nationalBox->appendChild($atHome);
        $atHome->appendChild($expectedDocument->createElement('product', $data['atHome']['product']));
        $options = $expectedDocument->createElement('options');
        foreach ($data['atHome']['options'] as $value) {
            foreach ($value as $key2 => $value2) {
                $element = $expectedDocument->createElement($key2);
                foreach ($value2 as $key3 => $value3) {
                    if ($key3 == '@attributes') {
                        foreach ($value3 as $key4 => $value4) {
                            $element->setAttribute($key4, $value4);
                        }
                    } else {
                        $element->appendChild(
                            $expectedDocument->createElement($key3, $value3)
                        );
                    }
                }

                $options->appendChild($element);
            }
        }
        $atHome->appendChild($options);
        $atHome->appendChild($expectedDocument->createElement('weight', $data['atHome']['weight']));
        $openingHours = $expectedDocument->createElement('openingHours');
        foreach ($data['atHome']['openingHours'] as $key => $value) {
            $openingHours->appendChild(
                $expectedDocument->createElement($key, $value)
            );
        }
        $atHome->appendChild($openingHours);
        $atHome->appendChild(
            $expectedDocument->createElement('desiredDeliveryPlace', $data['atHome']['desiredDeliveryPlace'])
        );
        $receiver = $expectedDocument->createElement('receiver');
        $atHome->appendChild($receiver);
        foreach ($data['atHome']['receiver'] as $key => $value) {
            $key = 'common:' . $key;
            if ($key == 'common:address') {
                $address = $expectedDocument->createElement($key);
                foreach ($value as $key2 => $value2) {
                    $key2 = 'common:' . $key2;
                    $address->appendChild(
                        $expectedDocument->createElement($key2, $value2)
                    );
                }
                $receiver->appendChild($address);
            } else {
                $receiver->appendChild(
                    $expectedDocument->createElement($key, $value)
                );
            }
        }

        $actualDocument = self::createDomDocument();
        $address = new Address(
            $data['atHome']['receiver']['address']['streetName'],
            $data['atHome']['receiver']['address']['number'],
            $data['atHome']['receiver']['address']['box'],
            $data['atHome']['receiver']['address']['postalCode'],
            $data['atHome']['receiver']['address']['locality'],
            $data['atHome']['receiver']['address']['countryCode']
        );

        $receiver = new Receiver();
        $receiver->setName($data['atHome']['receiver']['name']);
        $receiver->setCompany($data['atHome']['receiver']['company']);
        $receiver->setAddress($address);
        $receiver->setEmailAddress($data['atHome']['receiver']['emailAddress']);
        $receiver->setPhoneNumber($data['atHome']['receiver']['phoneNumber']);

        $openingHourDay = new OpeninghourDay('Monday', $data['atHome']['openingHours']['Monday']);

        $messaging = new Messaging(
            'infoNextDay',
            $data['atHome']['options'][0]['common:infoNextDay']['@attributes']['language'],
            $data['atHome']['options'][0]['common:infoNextDay']['common:emailAddress']
        );

        $atHome = new AtHome();
        $atHome->setProduct($data['atHome']['product']);
        $atHome->setWeight($data['atHome']['weight']);
        $atHome->setReceiver($receiver);
        $atHome->setDesiredDeliveryPlace($data['atHome']['desiredDeliveryPlace']);

        $atHome->addOpeningHour($openingHourDay);

        // I know, the line below is kinda bogus, but it will make sure all code is tested
        $atHome->setOpeningHours(array($openingHourDay));

        $atHome->addOption($messaging);

        // I know, the line below is kinda bogus, but it will make sure all code is tested
        $atHome->setOptions(array($messaging));

        $actualDocument->appendChild(
            $atHome->toXML($actualDocument)
        );

        $this->assertEquals($expectedDocument->saveXML(), $actualDocument->saveXML());
    }

    /**
     * Test validation in the setters
     */
    public function testFaultyProperties()
    {
        $atHome = new AtHome();

        try {
            $atHome->setProduct(str_repeat('a', 10));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', AtHome::getPossibleProductValues())
                ),
                $e->getMessage()
            );
        }
    }
}
