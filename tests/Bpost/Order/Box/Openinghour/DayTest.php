<?php
namespace Bpost;

require_once __DIR__ . '/../../../../../../../autoload.php';

use TijsVerkoyen\Bpost\Bpost\Order\Box\Openinghour\Day;

class DayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Day->toXMLArray
     */
    public function testToXMLArray()
    {
        $data = array(
            'Monday' => '10:00-17:00',
        );

        $day = new Day('Monday', '10:00-17:00');
        $xmlArray = $day->toXMLArray();

        $this->assertEquals($data, $xmlArray);
    }

    /**
     * Test validation in the setters
     */
    public function testFaultyProperties()
    {
        try {
            new Day(
                str_repeat('a', 10),
                '10:00-17:00'
            );
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', Day::getPossibleDayValues())
                ),
                $e->getMessage()
            );
        }
    }
}
