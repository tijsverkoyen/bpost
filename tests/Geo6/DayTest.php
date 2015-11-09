<?php

namespace Geo6;

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
    }

    /**
     * Tests Day->getDayIndex()
     */
    public function testGetDayIndex()
    {
        $day = new Day();
        $day->setDay('Monday');
        $this->assertEquals(1, $day->getDayIndex());
        $day->setDay('Tuesday');
        $this->assertEquals(2, $day->getDayIndex());
        $day->setDay('Wednesday');
        $this->assertEquals(3, $day->getDayIndex());
        $day->setDay('Thursday');
        $this->assertEquals(4, $day->getDayIndex());
        $day->setDay('Friday');
        $this->assertEquals(5, $day->getDayIndex());
        $day->setDay('Saturday');
        $this->assertEquals(6, $day->getDayIndex());
        $day->setDay('Sunday');
        $this->assertEquals(7, $day->getDayIndex());

        try {
            $day = new Day();
            $day->getDayIndex();
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals('Invalid day.', $e->getMessage());
        }

    }
}
