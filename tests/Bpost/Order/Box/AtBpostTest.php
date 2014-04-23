<?php
namespace Bpost;

require_once __DIR__ . '/../../../../../../autoload.php';

use TijsVerkoyen\Bpost\Bpost\Order\Box\AtBpost;
use TijsVerkoyen\Bpost\Bpost\Order\PugoAddress;

class AtBpostTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Address->toXMLArray
     */
    public function testToXMLArray()
    {
        $data = array(
            'atBpost' => array(
                'product' => 'bpack@bpost',
                'weight' => 2000,
                'pugoId' => '207500',
                'pugoName' => 'WIJNEGEM',
                'pugoAddress' => array(
                    'streetName' => 'Turnhoutsebaan',
                    'number' => '468',
                    'postalCode' => '2110',
                    'locality' => 'WIJNEGEM',
                    'countryCode' => 'BE',
                ),
                'receiverName' => 'Tijs Verkoyen',
                'receiverCompany' => 'Sumo Coders',
            ),
        );

        $pugoAddress = new PugoAddress(
            $data['atBpost']['pugoAddress']['streetName'],
            $data['atBpost']['pugoAddress']['number'],
            null,
            $data['atBpost']['pugoAddress']['postalCode'],
            $data['atBpost']['pugoAddress']['locality'],
            $data['atBpost']['pugoAddress']['countryCode']
        );

        $atBpost = new AtBpost();
        $atBpost->setProduct($data['atBpost']['product']);
        $atBpost->setWeight($data['atBpost']['weight']);

        $atBpost->setPugoId($data['atBpost']['pugoId']);
        $atBpost->setPugoName($data['atBpost']['pugoName']);
        $atBpost->setPugoAddress($pugoAddress);
        $atBpost->setReceiverName($data['atBpost']['receiverName']);
        $atBpost->setReceiverCompany($data['atBpost']['receiverCompany']);

        $xmlArray = $atBpost->toXMLArray();

        $this->assertEquals($data, $xmlArray);
    }

    /**
     * Test validation in the setters
     */
    public function testFaultyProperties()
    {
        $atBpost = new AtBpost();

        try {
            $atBpost->setProduct(str_repeat('a', 10));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', AtBpost::getPossibleProductValues())
                ),
                $e->getMessage()
            );
        }
    }
}
