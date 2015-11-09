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

        $this->assertSame($data['AMOpen'], $day->getAmOpen());
        $this->assertSame($data['AMClose'], $day->getAmClose());
        $this->assertSame($data['PMOpen'], $day->getPmOpen());
        $this->assertSame($data['PMClose'], $day->getPmClose());
    }

    /**
     * Tests Day->getDayIndex()
     */
    public function testGetDayIndex()
    {
        $day = new Day();
        $day->setDay('Monday');
        $this->assertSame(1, $day->getDayIndex());
        $day->setDay('Tuesday');
        $this->assertSame(2, $day->getDayIndex());
        $day->setDay('Wednesday');
        $this->assertSame(3, $day->getDayIndex());
        $day->setDay('Thursday');
        $this->assertSame(4, $day->getDayIndex());
        $day->setDay('Friday');
        $this->assertSame(5, $day->getDayIndex());
        $day->setDay('Saturday');
        $this->assertSame(6, $day->getDayIndex());
        $day->setDay('Sunday');
        $this->assertSame(7, $day->getDayIndex());

        try {
            $day = new Day();
            $day->getDayIndex();
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertSame('Invalid day.', $e->getMessage());
        }

    }
}
