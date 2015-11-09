<?php

namespace Geo6;

use TijsVerkoyen\Bpost\Geo6\Poi;

class PoiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Poi::createFromXml()
     */
    public function testCreateFromXml()
    {
        $data = array(
            'Id' => '405700',
            'Type' => '1',
            'Name' => 'ST-AMANDSBERG HOGEWEG',
            'Street' => 'Hogeweg',
            'Number' => '108',
            'Zip' => '9040',
            'City' => 'Sint-Amandsberg',
            'X' => 106482,
            'Y' => 195899,
            'Longitude' => 3.7479,
            'Latitude' => 51.0716,
        );

        // build xml
        $xmlString = '<record>';
        foreach ($data as $key => $value) {
            $xmlString .= '<' . $key . '>' . $value . '</' . $key . '>';
        }
        $xmlString .= '</record>';
        $xml = simplexml_load_string($xmlString);

        $poi = Poi::createFromXML($xml);

        $this->assertSame($data['Id'], $poi->getId());
        $this->assertSame($data['Type'], $poi->getType());
        $this->assertSame($data['Name'], $poi->getOffice());
        $this->assertSame($data['Street'], $poi->getStreet());
        $this->assertSame($data['Number'], $poi->getNr());
        $this->assertSame($data['Zip'], $poi->getZip());
        $this->assertSame($data['City'], $poi->getCity());
        $this->assertSame($data['X'], $poi->getX());
        $this->assertSame($data['Y'], $poi->getY());
        $this->assertSame($data['Longitude'], $poi->getLongitude());
        $this->assertSame($data['Latitude'], $poi->getLatitude());
    }
}
