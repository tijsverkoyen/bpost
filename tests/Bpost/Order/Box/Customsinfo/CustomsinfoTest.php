<?php
namespace Bpost;

require_once __DIR__ . '/../../../../../../../autoload.php';

use TijsVerkoyen\Bpost\Bpost\Order\Box\Customsinfo\CustomsInfo;
use TijsVerkoyen\Bpost\Exception;

class CustomsInfoTest extends \PHPUnit_Framework_TestCase
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
     * Tests Day->toXML
     */
    public function testToXML()
    {
        $data = array(
            'parcelValue' => '700',
            'contentDescription' => 'BOOK',
            'shipmentType' => 'DOCUMENTS',
            'parcelReturnInstructions' => 'RTS',
            'privateAddress' => false,
        );

        $expectedDocument = self::createDomDocument();
        $customsInfo = $expectedDocument->createElement('customsInfo');
        foreach ($data as $key => $value) {
            if ($key == 'privateAddress') {
                $value = ($value) ? 'true' : 'false';
            }
            $customsInfo->appendChild(
                $expectedDocument->createElement($key, $value)
            );
        }
        $expectedDocument->appendChild($customsInfo);

        $actualDocument = self::createDomDocument();
        $customsInfo = new CustomsInfo();
        $customsInfo->setParcelValue($data['parcelValue']);
        $customsInfo->setContentDescription($data['contentDescription']);
        $customsInfo->setShipmentType($data['shipmentType']);
        $customsInfo->setParcelReturnInstructions($data['parcelReturnInstructions']);
        $customsInfo->setPrivateAddress($data['privateAddress']);
        $actualDocument->appendChild(
            $customsInfo->toXML($actualDocument, null)
        );
        $this->assertEquals($expectedDocument, $actualDocument);
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
