<?php
namespace Bpost;

use TijsVerkoyen\Bpost\Bpost\Order\Line;

class LineTest extends \PHPUnit_Framework_TestCase
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
     * Tests Line->toXML
     */
    public function testToXML()
    {
        $data = array(
            'text' => 'just a random text',
            'nbOfItems' => time(),
        );

        $expectedDocument = self::createDomDocument();
        $line = $expectedDocument->createElement('orderLine');
        foreach ($data as $key => $value) {
            $line->appendChild(
                $expectedDocument->createElement($key, $value)
            );
        }
        $expectedDocument->appendChild($line);

        $actualDocument = self::createDomDocument();
        $line = new Line(
            $data['text'],
            $data['nbOfItems']
        );
        $actualDocument->appendChild(
            $line->toXML($actualDocument, null)
        );

        $this->assertEquals($expectedDocument, $actualDocument);
    }
}
