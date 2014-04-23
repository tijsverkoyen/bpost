<?php
namespace Bpost;

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../../../../autoload.php';

use TijsVerkoyen\Bpost\Bpost\Order;
use TijsVerkoyen\Bpost\Bpost\Order\Address;
use TijsVerkoyen\Bpost\Bpost\Order\Box;
use TijsVerkoyen\Bpost\Bpost\Order\Box\At247;
use TijsVerkoyen\Bpost\Bpost\Order\Line;
use TijsVerkoyen\Bpost\Bpost\Order\ParcelsDepotAddress;
use TijsVerkoyen\Bpost\Bpost\Order\Sender;

class OrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Order->toXMLArray
     */
    public function testToXMLArray()
    {
        $data = array(
            '@attributes' => array(
                'xmlns' => 'http://schema.post.be/shm/deepintegration/v3/',
            ),
            'accountId' => ACCOUNT_ID,
            'reference' => 'reference ' . time(),
            'costCenter' => 'costcenter ' . time(),
            'orderLine' => array(
                array(
                    'text' => 'Beer ' . time(),
                    'nbOfItems' => rand(1, 10),
                ),
                array(
                    'text' => 'Whisky ' . time(),
                    'nbOfItems' => rand(1, 10),
                ),
            ),
            'box' => array(
                array(
                    'sender' => array(
                        'name' => 'Tijs Verkoyen',
                        'company' => 'Sumo Coders',
                        'address' => array(
                            'streetName' => 'Afrikalaan',
                            'number' => '289',
                            'postalCode' => '9000',
                            'locality' => 'Gent',
                            'countryCode' => 'BE'
                        ),
                        'emailAddress' => 'bpost@verkoyen.eu',
                        'phoneNumber' => '+32 9 395 02 51',
                    ),
                    'nationalBox' => array(
                        'at24-7' => array(
                            'product' => 'bpack 24h Pro',
                            'weight' => 2000,
                            'parcelsDepotId' => '014472',
                            'parcelsDepotName' => 'WIJNEGEM',
                            'parcelsDepotAddress' => array(
                                'streetName' => 'Turnhoutsebaan',
                                'number' => '468',
                                'box' => 'A',
                                'postalCode' => '2110',
                                'locality' => 'Wijnegem',
                                'countryCode' => 'BE',
                            ),
                            'memberId' => '188565346',
                            'receiverName' => 'Tijs Verkoyen',
                            'receiverCompany' => 'Sumo Coders',
                        ),
                    ),
                    'remark' => 'remark ' . time(),
                ),
            ),
        );

        $address = new Address(
            $data['box'][0]['sender']['address']['streetName'],
            $data['box'][0]['sender']['address']['number'],
            null,
            $data['box'][0]['sender']['address']['postalCode'],
            $data['box'][0]['sender']['address']['locality'],
            $data['box'][0]['sender']['address']['countryCode']
        );

        $sender = new Sender();
        $sender->setName($data['box'][0]['sender']['name']);
        $sender->setCompany($data['box'][0]['sender']['company']);
        $sender->setAddress($address);
        $sender->setEmailAddress($data['box'][0]['sender']['emailAddress']);
        $sender->setPhoneNumber($data['box'][0]['sender']['phoneNumber']);

        $parcelsDepotAddress = new ParcelsDepotAddress(
            $data['box'][0]['nationalBox']['at24-7']['parcelsDepotAddress']['streetName'],
            $data['box'][0]['nationalBox']['at24-7']['parcelsDepotAddress']['number'],
            $data['box'][0]['nationalBox']['at24-7']['parcelsDepotAddress']['box'],
            $data['box'][0]['nationalBox']['at24-7']['parcelsDepotAddress']['postalCode'],
            $data['box'][0]['nationalBox']['at24-7']['parcelsDepotAddress']['locality'],
            $data['box'][0]['nationalBox']['at24-7']['parcelsDepotAddress']['countryCode']
        );

        $at247 = new At247();
        $at247->setProduct($data['box'][0]['nationalBox']['at24-7']['product']);
        $at247->setWeight($data['box'][0]['nationalBox']['at24-7']['weight']);
        $at247->setParcelsDepotId($data['box'][0]['nationalBox']['at24-7']['parcelsDepotId']);
        $at247->setParcelsDepotName($data['box'][0]['nationalBox']['at24-7']['parcelsDepotName']);
        $at247->setParcelsDepotAddress($parcelsDepotAddress);
        $at247->setMemberId($data['box'][0]['nationalBox']['at24-7']['memberId']);
        $at247->setReceiverName($data['box'][0]['nationalBox']['at24-7']['receiverName']);
        $at247->setReceiverCompany($data['box'][0]['nationalBox']['at24-7']['receiverCompany']);

        $box = new Box();
        $box->setSender($sender);
        $box->setNationalBox($at247);
        $box->setRemark($data['box'][0]['remark']);

        $order = new Order($data['reference']);
        $order->setCostCenter($data['costCenter']);

        $line1 = new Order\Line(
            $data['orderLine'][0]['text'],
            $data['orderLine'][0]['nbOfItems']
        );
        $line2 = new Line(
            $data['orderLine'][1]['text'],
            $data['orderLine'][1]['nbOfItems']
        );
        $order->addLine($line1);
        $order->addLine($line2);

        // I know, the line below is kinda bogus, but it will make sure all code is tested
        $order->setLines(array($line1, $line2));

        $order->addBox($box);

        // I know, the line below is kinda bogus, but it will make sure all code is tested
        $order->setBoxes(array($box));

        $xmlArray = $order->toXMLArray(ACCOUNT_ID);

        $this->assertEquals($data, $xmlArray);
    }
}
