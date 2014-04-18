<?php
namespace Bpost;

require_once __DIR__ . '/../../../../../autoload.php';

use TijsVerkoyen\Bpost\Bpost\Order\Address;
use TijsVerkoyen\Bpost\Bpost\Order\Box;
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

        $box = new Box();
        $box->setSender($sender);
        $box->setRemark($data['remark']);

        $xmlArray = $box->toXMLArray();

        $this->assertEquals($data, $xmlArray);
    }
}
