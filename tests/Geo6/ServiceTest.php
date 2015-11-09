<?php

namespace Geo6;

use TijsVerkoyen\Bpost\Geo6\Service;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Service::createFromXml()
     */
    public function testCreateFromXml()
    {
        $data = array(
            'category' => '2',
            'flag' => '10',
            'Name' => 'Loket met Bancontact/Mistercash',
        );

        // build xml
        $xmlString = '<Service';
        $xmlString .= ' category="' . $data['category'] . '"';
        $xmlString .= ' flag="' . $data['flag'] . '"';
        $xmlString .= '>';
        $xmlString .= $data['Name'];
        $xmlString .= '</Service>';
        $xml = simplexml_load_string($xmlString);

        $service = Service::createFromXML($xml);

        $this->assertEquals($data['category'], $service->getCategory());
        $this->assertEquals($data['flag'], $service->getFlag());
        $this->assertEquals($data['Name'], $service->getName());
    }
}
