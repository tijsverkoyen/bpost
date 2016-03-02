<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box\National;

use TijsVerkoyen\Bpost\BpostException;
use TijsVerkoyen\Bpost\Exception\BpostLogicException\BpostInvalidLengthException;

class ShopHandlingInstructionTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $value = str_repeat('a', 50);
        try {
            $test = new ShopHandlingInstruction($value);
            $this->assertEquals($value, $test->getValue());
        } catch (BpostException $ex) {
            $this->fail('Exception launched for valid value: "' . $value . '"');
        }

        $value = str_repeat('a', 51);
        try {
            new ShopHandlingInstruction($value);
            $this->fail('Exception uncaught for invalid value: "' . $value . '"');
        } catch (BpostInvalidLengthException $ex) {
            $this->assertTrue(true);
        }
    }

}
