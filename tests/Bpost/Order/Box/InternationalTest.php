<?php
namespace Bpost;

require_once __DIR__ . '/../../../../../../autoload.php';

use TijsVerkoyen\Bpost\Bpost\Order\Address;
use TijsVerkoyen\Bpost\Bpost\Order\Box\Customsinfo\CustomsInfo;
use TijsVerkoyen\Bpost\Bpost\Order\Box\International;
use TijsVerkoyen\Bpost\Bpost\Order\Receiver;
use TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Messaging;

class InternationalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests International->toXMLArray
     */
    public function testToXMLArray()
    {
        $data = array(
            'international' => array(
                'product' => 'bpack World Express Pro',
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
                'parcelWeight' => 2000,
                'customsInfo' => array(
                    'parcelValue' => 700,
                    'contentDescription' => 'BOOK',
                    'shipmentType' => 'DOCUMENTS',
                    'parcelReturnInstructions' => 'RTS',
                    'privateAddress' => false,
                )
            ),
        );

        $address = new Address(
            $data['international']['receiver']['address']['streetName'],
            $data['international']['receiver']['address']['number'],
            $data['international']['receiver']['address']['box'],
            $data['international']['receiver']['address']['postalCode'],
            $data['international']['receiver']['address']['locality'],
            $data['international']['receiver']['address']['countryCode']
        );

        $receiver = new Receiver();
        $receiver->setName($data['international']['receiver']['name']);
        $receiver->setCompany($data['international']['receiver']['company']);
        $receiver->setAddress($address);
        $receiver->setEmailAddress($data['international']['receiver']['emailAddress']);
        $receiver->setPhoneNumber($data['international']['receiver']['phoneNumber']);

        $customsInfo = new CustomsInfo();
        $customsInfo->setParcelValue($data['international']['customsInfo']['parcelValue']);
        $customsInfo->setContentDescription($data['international']['customsInfo']['contentDescription']);
        $customsInfo->setShipmentType($data['international']['customsInfo']['shipmentType']);
        $customsInfo->setParcelReturnInstructions($data['international']['customsInfo']['parcelReturnInstructions']);
        $customsInfo->setPrivateAddress($data['international']['customsInfo']['privateAddress']);

        $international = new International();
        $international->setProduct($data['international']['product']);
        $international->addOption(
            new Messaging(
                'infoNextDay',
                $data['international']['options'][0]['infoNextDay']['@attributes']['language'],
                $data['international']['options'][0]['infoNextDay']['emailAddress']
            )
        );
        $international->setReceiver($receiver);
        $international->setParcelWeight($data['international']['parcelWeight']);
        $international->setCustomsInfo($customsInfo);

        $xmlArray = $international->toXMLArray();

        $this->assertEquals($data, $xmlArray);
    }

    /**
     * Test validation in the setters
     */
    public function testFaultyProperties()
    {
        $international = new International();

        try {
            $international->setProduct(str_repeat('a', 10));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', International::getPossibleProductValues())
                ),
                $e->getMessage()
            );
        }
    }
}
