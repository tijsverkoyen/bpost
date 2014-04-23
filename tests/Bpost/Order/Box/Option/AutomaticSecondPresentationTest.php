<?php
namespace Bpost;

require_once __DIR__ . '/../../../../../../../autoload.php';

use TijsVerkoyen\Bpost\Bpost\Order\Box\Option\AutomaticSecondPresentation;

class AutomaticSecondPresentationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests AutomaticSecondPresentation->toXMLArray
     */
    public function testToXMLArray()
    {
        $data = array(
            'automaticSecondPresentation' => array(),
        );

        $automaticSecondPresentation = new AutomaticSecondPresentation();
        $xmlArray = $automaticSecondPresentation->toXMLArray();

        $this->assertEquals($data, $xmlArray);
    }
}
