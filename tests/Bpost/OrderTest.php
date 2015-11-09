<?php
namespace Bpost;

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
     * Tests Order->toXML
     */
    public function testToXML()
    {
        $accountId = 1234567;

        $data = array(
            'accountId' => $accountId,
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

        $expectedDocument = self::createDomDocument();
        $order = $expectedDocument->createElement(
            'tns:order'
        );
        $order->setAttribute(
            'xmlns:common',
            'http://schema.post.be/shm/deepintegration/v3/common'
        );
        $order->setAttribute(
            'xmlns:tns',
            'http://schema.post.be/shm/deepintegration/v3/'
        );
        $order->setAttribute(
            'xmlns',
            'http://schema.post.be/shm/deepintegration/v3/national'
        );
        $order->setAttribute(
            'xmlns:international',
            'http://schema.post.be/shm/deepintegration/v3/international'
        );
        $order->setAttribute(
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance'
        );
        $order->setAttribute(
            'xsi:schemaLocation',
            'http://schema.post.be/shm/deepintegration/v3/'
        );
        $expectedDocument->appendChild($order);
        $order->appendChild(
            $expectedDocument->createElement('tns:accountId', $data['accountId'])
        );
        $order->appendChild(
            $expectedDocument->createElement('tns:reference', $data['reference'])
        );
        $order->appendChild(
            $expectedDocument->createElement('tns:costCenter', $data['costCenter'])
        );
        foreach ($data['orderLine'] as $row) {
            $line = $expectedDocument->createElement('tns:orderLine');
            $line->appendChild(
                $expectedDocument->createElement('tns:text', $row['text'])
            );
            $line->appendChild(
                $expectedDocument->createElement('tns:nbOfItems', $row['nbOfItems'])
            );
            $order->appendChild($line);
        }
        $box = $expectedDocument->createElement('tns:box');
        $order->appendChild($box);
        $sender = $expectedDocument->createElement('tns:sender');
        foreach ($data['box'][0]['sender'] as $key => $value) {
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
        $box->appendChild($sender);
        $nationalBox = $expectedDocument->createElement('tns:nationalBox');
        $at247 = $expectedDocument->createElement('at24-7');
        $nationalBox->appendChild($at247);
        $expectedDocument->appendChild($nationalBox);
        foreach ($data['box'][0]['nationalBox']['at24-7'] as $key => $value) {
            if ($key == 'parcelsDepotAddress') {
                $address = $expectedDocument->createElement($key);
                foreach ($value as $key2 => $value2) {
                    $key2 = 'common:' . $key2;
                    $address->appendChild(
                        $expectedDocument->createElement($key2, $value2)
                    );
                }
                $at247->appendChild($address);
            } else {
                $at247->appendChild(
                    $expectedDocument->createElement($key, $value)
                );
            }
        }
        $box->appendChild($nationalBox);
        $box->appendChild(
            $expectedDocument->createElement('tns:remark', $data['box'][0]['remark'])
        );

        $actualDocument = self::createDomDocument();
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

        $actualDocument->appendChild(
            $order->toXML($actualDocument, $accountId)
        );

        $this->assertEquals($expectedDocument, $actualDocument);

        try {
            $xml = simplexml_load_string(
                '<order>
                </order>'
            );
            $order = Order::createFromXML($xml);
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertSame('No reference found.', $e->getMessage());
        }
    }
}
