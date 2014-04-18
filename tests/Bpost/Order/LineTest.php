<?php
namespace Bpost;

require_once __DIR__ . '/../../../../../autoload.php';

use TijsVerkoyen\Bpost\Bpost\Order\Line;

class LineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Line->toXMLArray
     */
    public function testToXMLArray()
    {
        $data = array(
            'nbOfItems' => time(),
            'text' => 'just a random text',
        );

        $line = new Line(
            $data['text'],
            $data['nbOfItems']
        );

        $xmlArray = $line->toXMLArray();

        $this->assertEquals($data, $xmlArray);
    }
}
