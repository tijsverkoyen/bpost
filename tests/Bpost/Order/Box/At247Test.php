<?php
namespace Bpost;

require_once __DIR__ . '/../../../../../../autoload.php';

use TijsVerkoyen\Bpost\Bpost\Order\Box\At247;
use TijsVerkoyen\Bpost\Bpost\Order\ParcelsDepotAddress;

class At247Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests At247->toXMLArray
     */
    public function testToXMLArray()
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
                'receiverName' => 'Tijs Verkoyen',
                'receiverCompany' => 'Sumo Coders',
            ),
        );

        $parcelsDepotAddress = new ParcelsDepotAddress(
            $data['at24-7']['parcelsDepotAddress']['streetName'],
            $data['at24-7']['parcelsDepotAddress']['number'],
            $data['at24-7']['parcelsDepotAddress']['box'],
            $data['at24-7']['parcelsDepotAddress']['postalCode'],
            $data['at24-7']['parcelsDepotAddress']['locality'],
            $data['at24-7']['parcelsDepotAddress']['countryCode']
        );

        $at247 = new At247();
        $at247->setProduct($data['at24-7']['product']);
        $at247->setWeight($data['at24-7']['weight']);
        $at247->setParcelsDepotId($data['at24-7']['parcelsDepotId']);
        $at247->setParcelsDepotName($data['at24-7']['parcelsDepotName']);
        $at247->setParcelsDepotAddress($parcelsDepotAddress);
        $at247->setMemberId($data['at24-7']['memberId']);
        $at247->setReceiverName($data['at24-7']['receiverName']);
        $at247->setReceiverCompany($data['at24-7']['receiverCompany']);

        $xmlArray = $at247->toXMLArray();

        $this->assertEquals($data, $xmlArray);
    }

    /**
     * Test validation in the setters
     */
    public function testFaultyProperties()
    {
        $at247 = new At247();

        try {
            $at247->setProduct(str_repeat('a', 10));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', At247::getPossibleProductValues())
                ),
                $e->getMessage()
            );
        }
    }
}
