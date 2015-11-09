<?php
namespace Bpost;

use TijsVerkoyen\Bpost\Bpost\Order\Address;
use TijsVerkoyen\Bpost\Bpost\Order\Box\Customsinfo\CustomsInfo;
use TijsVerkoyen\Bpost\Bpost\Order\Box\International;
use TijsVerkoyen\Bpost\Bpost\Order\Receiver;
use TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Messaging;

class InternationalTest extends \PHPUnit_Framework_TestCase
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
     * Tests International->toXML
     */
    public function testToXML()
    {
        $data = array(
            'international' => array(
                'product' => 'bpack World Express Pro',
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

        $expectedDocument = self::createDomDocument();
        $nationalBox = $expectedDocument->createElement('internationalBox');
        $expectedDocument->appendChild($nationalBox);
        $international = $expectedDocument->createElement('international:international');
        $nationalBox->appendChild($international);
        $international->appendChild(
            $expectedDocument->createElement('international:product', $data['international']['product'])
        );
        $options = $expectedDocument->createElement('international:options');
        foreach ($data['international']['options'] as $value) {
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
        $international->appendChild($options);
        $receiver = $expectedDocument->createElement('international:receiver');
        $international->appendChild($receiver);
        foreach ($data['international']['receiver'] as $key => $value) {
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
        $international->appendChild(
            $expectedDocument->createElement('international:parcelWeight', $data['international']['parcelWeight'])
        );
        $customsInfo = $expectedDocument->createElement('international:customsInfo');
        foreach ($data['international']['customsInfo'] as $key => $value) {
            if ($key == 'privateAddress') {
                $value = ($value) ? 'true' : 'false';
            }
            $customsInfo->appendChild(
                $expectedDocument->createElement('international:' . $key, $value)
            );
        }
        $international->appendChild($customsInfo);

        $actualDocument = self::createDomDocument();
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

        $messaging = new Messaging(
            'infoNextDay',
            $data['international']['options'][0]['common:infoNextDay']['@attributes']['language'],
            $data['international']['options'][0]['common:infoNextDay']['common:emailAddress']
        );

        $international = new International();
        $international->setProduct($data['international']['product']);
        $international->setReceiver($receiver);
        $international->setParcelWeight($data['international']['parcelWeight']);
        $international->setCustomsInfo($customsInfo);

        $international->addOption($messaging);

        // I know, the line below is kinda bogus, but it will make sure all code is tested
        $international->setOptions(array($messaging));

        $actualDocument->appendChild(
            $international->toXML($actualDocument)
        );

        $this->assertEquals($expectedDocument->saveXML(), $actualDocument->saveXML());
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
