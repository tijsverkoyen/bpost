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
    }

    /**
     * Tests Day->getDayIndex()
     */
    public function testGetDayIndex()
    {
        $days = array(
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        );

        $data = array(
            'AMOpen' => '9:00',
            'AMClose' => '12:00',
            'PMOpen' => '13:00',
            'PMClose' => '18:00',
        );

        foreach ($days as $index => $name) {
            // build xml
            $xmlString = '<' . $name . '>';
            foreach ($data as $key => $value) {
                $xmlString .= '<' . $key . '>' . $value . '</' . $key . '>';
            }
            $xmlString .= '</' . $name . '>';
            $xml = simplexml_load_string($xmlString);

            $day = Day::createFromXML($xml);
            $this->assertEquals($index, $day->getDayIndex());
        }

        try {
            $day = new Day();
            $day->getDayIndex();
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals('Invalid day.', $e->getMessage());
        }
    }
}
