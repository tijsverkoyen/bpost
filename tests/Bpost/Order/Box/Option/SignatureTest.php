<?php
namespace Bpost;

require_once __DIR__ . '/../../../../../../../autoload.php';

use TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Signature;

class SignatureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Signature->toXMLArray
     */
    public function testToXMLArray()
    {
        $data = array(
            'signed' => array(),
        );

        $signature = new Signature();
        $xmlArray = $signature->toXMLArray();

        $this->assertEquals($data, $xmlArray);
    }
}
