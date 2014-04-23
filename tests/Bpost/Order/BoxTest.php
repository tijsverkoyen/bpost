<?php
namespace Bpost;

require_once __DIR__ . '/../../../../../autoload.php';

use TijsVerkoyen\Bpost\Bpost\Order\Address;
use TijsVerkoyen\Bpost\Bpost\Order\Box;
use TijsVerkoyen\Bpost\Bpost\Order\Box\AtHome;
use TijsVerkoyen\Bpost\Bpost\Order\Box\International;
use TijsVerkoyen\Bpost\Bpost\Order\Sender;

class BoxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Box->toXMLArray
     */
    public function testToXMLArray()
    {
        $data = array(
            'sender' => array(
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
            'nationalBox' => array(
                'atHome' => array(
                    'product' => 'bpack 24h Pro',
                    'weight' => 2000,
                    'receiver' => array(
                        'name' => 'Tijs Verkoyen',
                        'company' => 'Sumo Coders',
                        'address' => array(
                            'streetName' => 'Kerkstraat',
                            'number' => '108',
                            'postalCode' => '9050',
                            'locality' => 'Gentbrugge',
                            'countryCode' => 'BE',
                        ),
                        'emailAddress' => 'bpost@verkoyen.eu',
                        'phoneNumber' => '+32 9 395 02 51',
                    ),
                ),
            ),
            'remark' => 'remark',
        );

        $address = new Address(
            $data['sender']['address']['streetName'],
            $data['sender']['address']['number'],
            $data['sender']['address']['box'],
            $data['sender']['address']['postalCode'],
            $data['sender']['address']['locality'],
            $data['sender']['address']['countryCode']
        );

        $sender = new Sender();
        $sender->setName($data['sender']['name']);
        $sender->setCompany($data['sender']['company']);
        $sender->setAddress($address);
        $sender->setEmailAddress($data['sender']['emailAddress']);
        $sender->setPhoneNumber($data['sender']['phoneNumber']);

        $address = new Address(
            $data['nationalBox']['atHome']['receiver']['address']['streetName'],
            $data['nationalBox']['atHome']['receiver']['address']['number'],
            null,
            $data['nationalBox']['atHome']['receiver']['address']['postalCode'],
            $data['nationalBox']['atHome']['receiver']['address']['locality'],
            $data['nationalBox']['atHome']['receiver']['address']['countryCode']
        );

        $receiver = new Sender();
        $receiver->setAddress($address);
        $receiver->setName($data['nationalBox']['atHome']['receiver']['name']);
        $receiver->setCompany($data['nationalBox']['atHome']['receiver']['company']);
        $receiver->setPhoneNumber($data['nationalBox']['atHome']['receiver']['phoneNumber']);
        $receiver->setEmailAddress($data['nationalBox']['atHome']['receiver']['emailAddress']);

        $atHome = new AtHome();
        $atHome->setProduct($data['nationalBox']['atHome']['product']);
        $atHome->setWeight($data['nationalBox']['atHome']['weight']);
        $atHome->setReceiver($receiver);

        $box = new Box();
        $box->setSender($sender);
        $box->setNationalBox($atHome);
        $box->setRemark($data['remark']);

        $xmlArray = $box->toXMLArray();
        $this->assertEquals($data, $xmlArray);


        $data = array(
            'sender' => array(
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
            'internationalBox' => array(
                'international' => array(
                    'product' => 'bpack World Express Pro',
                    'receiver' => array(
                        'name' => 'Tijs Verkoyen',
                        'company' => 'Sumo Coders',
                        'address' => array(
                            'streetName' => 'Kerkstraat',
                            'number' => '108',
                            'postalCode' => '9050',
                            'locality' => 'Gentbrugge',
                            'countryCode' => 'BE',
                        ),
                        'emailAddress' => 'bpost@verkoyen.eu',
                        'phoneNumber' => '+32 9 395 02 51',
                    ),
                ),
            ),
            'remark' => 'remark',
        );

        $address = new Address(
            $data['sender']['address']['streetName'],
            $data['sender']['address']['number'],
            $data['sender']['address']['box'],
            $data['sender']['address']['postalCode'],
            $data['sender']['address']['locality'],
            $data['sender']['address']['countryCode']
        );

        $sender = new Sender();
        $sender->setName($data['sender']['name']);
        $sender->setCompany($data['sender']['company']);
        $sender->setAddress($address);
        $sender->setEmailAddress($data['sender']['emailAddress']);
        $sender->setPhoneNumber($data['sender']['phoneNumber']);

        $address = new Address(
            $data['internationalBox']['international']['receiver']['address']['streetName'],
            $data['internationalBox']['international']['receiver']['address']['number'],
            null,
            $data['internationalBox']['international']['receiver']['address']['postalCode'],
            $data['internationalBox']['international']['receiver']['address']['locality'],
            $data['internationalBox']['international']['receiver']['address']['countryCode']
        );

        $receiver = new Sender();
        $receiver->setAddress($address);
        $receiver->setName($data['internationalBox']['international']['receiver']['name']);
        $receiver->setCompany($data['internationalBox']['international']['receiver']['company']);
        $receiver->setPhoneNumber($data['internationalBox']['international']['receiver']['phoneNumber']);
        $receiver->setEmailAddress($data['internationalBox']['international']['receiver']['emailAddress']);

        $international = new International();
        $international->setProduct($data['internationalBox']['international']['product']);
        $international->setReceiver($receiver);

        $box = new Box();
        $box->setSender($sender);
        $box->setInternationalBox($international);
        $box->setRemark($data['remark']);

        $xmlArray = $box->toXMLArray();
        $this->assertEquals($data, $xmlArray);
    }
}
