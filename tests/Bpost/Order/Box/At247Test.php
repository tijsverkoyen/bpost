<?php
namespace Bpost;

use Bpost\BpostApiClient\Bpost\Order\Box\At247;
use Bpost\BpostApiClient\Bpost\Order\Box\National\UnregisteredParcelLockerMember;
use Bpost\BpostApiClient\Bpost\Order\ParcelsDepotAddress;
use Bpost\BpostApiClient\Common\BasicAttribute\Language;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;

class At247Test extends \PHPUnit_Framework_TestCase
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
     * Tests At247->toXML
     *
     * @warning
     * That is a bad test, we cannot have a memberId AND an unregisteredParcelLockerMember
     * We must to have a XML with memberId, another one with unregisteredParcelLockerMember and another one without (to see comportment)
     */
    public function testToXML()
    {
        $data = array(
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
                'unregisteredParcelLockerMember' => array(
                    'language' => 'EN',
                    'mobilePhone' => '0471000000',
                    'emailAddress' => 'pomme@antidot.com'
                ), // Bad test: We cannot have a memberId AND an unregisteredParcelLockerMember
                'receiverName' => 'Tijs Verkoyen',
                'receiverCompany' => 'Sumo Coders',
                'requestedDeliveryDate' => '2016-03-16',
            ),
        );

        $expectedDocument = self::createDomDocument();
        $nationalBox = $expectedDocument->createElement('nationalBox');
        $at247 = $expectedDocument->createElement('at24-7');
        $nationalBox->appendChild($at247);
        $expectedDocument->appendChild($nationalBox);
        foreach ($data['at24-7'] as $key => $value) {
            if ($key == 'parcelsDepotAddress') {
                $address = $expectedDocument->createElement($key);
                foreach ($value as $key2 => $value2) {
                    $key2 = 'common:' . $key2;
                    $address->appendChild(
                        $expectedDocument->createElement($key2, $value2)
                    );
                }
                $at247->appendChild($address);
            } elseif ($key == 'unregisteredParcelLockerMember') {
                $child = $expectedDocument->createElement($key);
                foreach ($value as $key2 => $value2) {
                    $child->appendChild(
                        $expectedDocument->createElement($key2, $value2)
                    );
                }
                $at247->appendChild($child);
            } else {
                $at247->appendChild(
                    $expectedDocument->createElement($key, $value)
                );
            }
        }

        $actualDocument = self::createDomDocument();
        $parcelsDepotAddress = new ParcelsDepotAddress(
            $data['at24-7']['parcelsDepotAddress']['streetName'],
            $data['at24-7']['parcelsDepotAddress']['number'],
            $data['at24-7']['parcelsDepotAddress']['box'],
            $data['at24-7']['parcelsDepotAddress']['postalCode'],
            $data['at24-7']['parcelsDepotAddress']['locality'],
            $data['at24-7']['parcelsDepotAddress']['countryCode']
        );
        $unregisteredParcelLockerMember = new UnregisteredParcelLockerMember();
        $unregisteredParcelLockerMember->setLanguage($data['at24-7']['unregisteredParcelLockerMember']['language']);
        $unregisteredParcelLockerMember->setMobilePhone($data['at24-7']['unregisteredParcelLockerMember']['mobilePhone']);
        $unregisteredParcelLockerMember->setEmailAddress($data['at24-7']['unregisteredParcelLockerMember']['emailAddress']);

        $at247 = new At247();
        $at247->setProduct($data['at24-7']['product']);
        $at247->setWeight($data['at24-7']['weight']);
        $at247->setRequestedDeliveryDate($data['at24-7']['requestedDeliveryDate']);
        $at247->setParcelsDepotId($data['at24-7']['parcelsDepotId']);
        $at247->setParcelsDepotName($data['at24-7']['parcelsDepotName']);
        $at247->setParcelsDepotAddress($parcelsDepotAddress);
        $at247->setMemberId($data['at24-7']['memberId']);
        $at247->setUnregisteredParcelLockerMember($unregisteredParcelLockerMember);
        $at247->setReceiverName($data['at24-7']['receiverName']);
        $at247->setReceiverCompany($data['at24-7']['receiverCompany']);
        $actualDocument->appendChild(
            $at247->toXML($actualDocument)
        );

        $this->assertSame($expectedDocument->saveXML(), $actualDocument->saveXML());
    }

    /**
     * Test validation in the setters
     */
    public function testFaultyProperties()
    {
        $at247 = new At247();

        try {
            $at247->setProduct(str_repeat('a', 10));
            $this->fail('BpostInvalidValueException not launched');
        } catch (BpostInvalidValueException $e) {
            // Nothing, the exception is good
        } catch (\Exception $e) {
            $this->fail('BpostInvalidValueException not caught');
        }

        // Exceptions were caught,
        $this->assertTrue(true);
    }
}
