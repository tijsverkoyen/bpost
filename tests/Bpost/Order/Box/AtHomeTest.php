<?php
namespace Bpost;

require_once __DIR__ . '/../../../../../../autoload.php';

use TijsVerkoyen\Bpost\Bpost\Order\Address;
use TijsVerkoyen\Bpost\Bpost\Order\Box\AtHome;
use TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Messaging;
use TijsVerkoyen\Bpost\Bpost\Order\Receiver;
use TijsVerkoyen\Bpost\Bpost\Order\Box\Openinghour\Day as OpeninghourDay;

class AtHomeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Address->toXMLArray
     */
    public function testToXMLArray()
    {
        $data = array(
            'atHome' => array(
                'product' => 'bpack 24h Pro',
                'options' => array(
                    array(
                        'infoNextDay' => array(
                            '@attributes' => array(
                                'language' => 'NL',
                            ),
                            'emailAddress' => 'bpost@verkoyen.eu',
                        )
                    )
                ),
                'weight' => 2000,
                'openingHours' => array(
                    array(
                        'Monday' => '10:00-17:00',
                    )
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

        $openingHourDay = new OpeninghourDay('Monday', $data['atHome']['openingHours'][0]['Monday']);

        $messaging = new Messaging(
            'infoNextDay',
            $data['atHome']['options'][0]['infoNextDay']['@attributes']['language'],
            $data['atHome']['options'][0]['infoNextDay']['emailAddress']
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

        $xmlArray = $atHome->toXMLArray();

        $this->assertEquals($data, $xmlArray);
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
