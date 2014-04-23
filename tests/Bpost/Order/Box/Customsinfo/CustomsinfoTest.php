<?php
namespace Bpost;

require_once __DIR__ . '/../../../../../../../autoload.php';

use TijsVerkoyen\Bpost\Bpost\Order\Box\Customsinfo\CustomsInfo;
use TijsVerkoyen\Bpost\Exception;

class CustomsInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Day->toXMLArray
     */
    public function testToXMLArray()
    {
        $data = array(
            'parcelValue' => '700',
            'contentDescription' => 'BOOK',
            'shipmentType' => 'DOCUMENTS',
            'parcelReturnInstructions' => 'RTS',
            'privateAddress' => false,
        );

        $customsInfo = new CustomsInfo();
        $customsInfo->setParcelValue($data['parcelValue']);
        $customsInfo->setContentDescription($data['contentDescription']);
        $customsInfo->setShipmentType($data['shipmentType']);
        $customsInfo->setParcelReturnInstructions($data['parcelReturnInstructions']);
        $customsInfo->setPrivateAddress($data['privateAddress']);
        $xmlArray = $customsInfo->toXMLArray();

        $this->assertEquals($data, $xmlArray);
    }

    /**
     * Test validation in the setters
     */
    public function testFaultyProperties()
    {
        $customsInfo = new CustomsInfo();

        try {
            $customsInfo->setContentDescription(
                str_repeat('a', 51)
            );
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals('Invalid length, maximum is 50.', $e->getMessage());
        }

        try {
            $customsInfo->setParcelReturnInstructions(
                str_repeat('a', 10)
            );
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', CustomsInfo::getPossibleParcelReturnInstructionValues())
                ),
                $e->getMessage()
            );
        }

        try {
            $customsInfo->setShipmentType(
                str_repeat('a', 10)
            );
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', CustomsInfo::getPossibleShipmentTypeValues())
                ),
                $e->getMessage()
            );
        }
    }
}
