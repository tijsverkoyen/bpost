<?php

namespace Geo6;

require_once __DIR__ . '/../../../../autoload.php';

use TijsVerkoyen\Bpost\Geo6\Day;

class DayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Day::createFromXml()
     */
    public function testCreateFromXml()
    {
        $data = array(
            'AMOpen' => '9:00',
            'AMClose' => '12:00',
            'PMOpen' => '13:00',
            'PMClose' => '18:00',
        );

        // build xml
        $xmlString = '<Monday>';
        foreach ($data as $key => $value) {
            $xmlString .= '<' . $key . '>' . $value . '</' . $key . '>';
        }
        $xmlString .= '</Monday>';
        $xml = simplexml_load_string($xmlString);

        $day = Day::createFromXML($xml);

        $this->assertEquals($data['AMOpen'], $day->getAmOpen());
        $this->assertEquals($data['AMClose'], $day->getAmClose());
        $this->assertEquals($data['PMOpen'], $day->getPmOpen());
        $this->assertEquals($data['PMClose'], $day->getPmClose());
        $this->assertEquals(1, $day->getDayIndex());
    }
}
