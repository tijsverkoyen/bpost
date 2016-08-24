<?php
namespace Bpost;

use Bpost\BpostApiClient\Bpost\Order\Box\OpeningHour\Day;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;

class DayTest extends \PHPUnit_Framework_TestCase
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
            'Monday' => '10:00-17:00',
        );

        $expectedDocument = self::createDomDocument();
        foreach ($data as $key => $value) {
            $expectedDocument->appendChild(
                $expectedDocument->createElement(
                    $key,
                    $value
                )
            );
        }

        $actualDocument = self::createDomDocument();

        foreach ($data as $key => $value) {
            $day = new Day('Monday', '10:00-17:00');
            $actualDocument->appendChild(
                $day->toXML($actualDocument, null)
            );
        }

        $this->assertEquals($expectedDocument, $actualDocument);

        $data = array(
            'Monday' => '10:00-17:00',
        );

        $expectedDocument = self::createDomDocument();
        foreach ($data as $key => $value) {
            $expectedDocument->appendChild(
                $expectedDocument->createElement(
                    'foo:' . $key,
                    $value
                )
            );
        }

        $actualDocument = self::createDomDocument();

        foreach ($data as $key => $value) {
            $day = new Day('Monday', '10:00-17:00');
            $actualDocument->appendChild(
                $day->toXML($actualDocument, 'foo')
            );
        }

        $this->assertSame($expectedDocument->saveXML(), $actualDocument->saveXML());
    }

    /**
     * Test validation in the setters
     */
    public function testFaultyProperties()
    {
        try {
            new Day(str_repeat('a', 10), '10:00-17:00');
            $this->fail('BpostInvalidValueException not launched');
        } catch (BpostInvalidValueException $e) {
            // Nothing, the exception is good
        } catch (\Exception $e) {
            $this->fail('BpostInvalidValueException not caught');
        }

        // Exceptions were caught,
        $this->assertTrue(true);
    }
}
