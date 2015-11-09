<?php
namespace Bpost;

use TijsVerkoyen\Bpost\Bpost\Order\Address;
use TijsVerkoyen\Bpost\Bpost\Order\Box;
use TijsVerkoyen\Bpost\Bpost\Order\Box\AtHome;
use TijsVerkoyen\Bpost\Bpost\Order\Box\International;
use TijsVerkoyen\Bpost\Bpost\Order\Receiver;
use TijsVerkoyen\Bpost\Bpost\Order\Sender;

class BoxTest extends \PHPUnit_Framework_TestCase
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
     * Tests Box->toXML
     */
    public function testNationalToXML()
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

        $expectedDocument = self::createDomDocument();
        $box = $expectedDocument->createElement('box');
        $expectedDocument->appendChild($box);
        $sender = $expectedDocument->createElement('sender');
        foreach ($data['sender'] as $key => $value) {
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
        $nationalBox = $expectedDocument->createElement('nationalBox');
        $atHome = $expectedDocument->createElement('atHome');
        $nationalBox->appendChild($atHome);
        $atHome->appendChild($expectedDocument->createElement('product', $data['nationalBox']['atHome']['product']));
        $atHome->appendChild($expectedDocument->createElement('weight', $data['nationalBox']['atHome']['weight']));
        $receiver = $expectedDocument->createElement('receiver');
        $atHome->appendChild($receiver);
        foreach ($data['nationalBox']['atHome']['receiver'] as $key => $value) {
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
        $box->appendChild($sender);
        $box->appendChild($nationalBox);
        $box->appendChild($expectedDocument->createElement('remark', $data['remark']));

        $actualDocument = self::createDomDocument();
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

        $receiver = new Receiver();
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

        $actualDocument->appendChild(
            $box->toXML($actualDocument, null)
        );

        $this->assertEquals($expectedDocument->saveXML(), $actualDocument->saveXML());
    }

    /**
     * Tests Box->toXML
     */
    public function testInternationalToXML()
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
        $expectedDocument = self::createDomDocument();
        $box = $expectedDocument->createElement('box');
        $expectedDocument->appendChild($box);
        $sender = $expectedDocument->createElement('sender');
        foreach ($data['sender'] as $key => $value) {
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
        $nationalBox = $expectedDocument->createElement('internationalBox');
        $atHome = $expectedDocument->createElement('international:international');
        $nationalBox->appendChild($atHome);
        $atHome->appendChild(
            $expectedDocument->createElement(
                'international:product',
                $data['internationalBox']['international']['product']
            )
        );
        $receiver = $expectedDocument->createElement('international:receiver');
        $atHome->appendChild($receiver);
        foreach ($data['internationalBox']['international']['receiver'] as $key => $value) {
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
        $box->appendChild($sender);
        $box->appendChild($nationalBox);
        $box->appendChild($expectedDocument->createElement('remark', $data['remark']));

        $actualDocument = self::createDomDocument();
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

        $receiver = new Receiver();
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

        $actualDocument->appendChild(
            $box->toXML($actualDocument, null)
        );

        $this->assertEquals($expectedDocument->saveXML(), $actualDocument->saveXML());
    }

    /**
     * Test validation in the setters
     */
    public function testFaultyProperties()
    {
        $box = new Box();

        try {
            $box->setStatus(str_repeat('a', 10));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals(
                'Invalid value, possible values are: ' . implode(', ', Box::getPossibleStatusValues()) . '.',
                $e->getMessage()
            );
        }
    }
}
