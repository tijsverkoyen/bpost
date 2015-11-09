<?php
namespace Bpost\Bpack;

use TijsVerkoyen\Bpost\Bpack247\CustomerPackStation;

class CustomerPackStationTest extends \PHPUnit_Framework_TestCase
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
     * Tests CustomerPackStation->toXML
     */
    public function testCreateFromXML()
    {
        $data = array(
            'CustomLabel' => 'CustomLabel',
            'OrderNumber' => '1',
            'PackstationID' => '14472',
        );

        $document = self::createDomDocument();
        $customerPackStationElement = $document->createElement('CustomerPackStation');
        foreach ($data as $key => $value) {
            $customerPackStationElement->appendChild(
                $document->createElement($key, $value)
            );
        }
        $document->appendChild($customerPackStationElement);

        $customerPackStation = CustomerPackStation::createFromXML(
            simplexml_load_string(
                $document->saveXML()
            )
        );

        $this->assertEquals($data['CustomLabel'], $customerPackStation->getCustomLabel());
        $this->assertEquals($data['OrderNumber'], $customerPackStation->getOrderNumber());
        $this->assertEquals($data['PackstationID'], $customerPackStation->getPackstationId());
    }
}
