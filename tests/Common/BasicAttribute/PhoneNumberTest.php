<?php

use Bpost\BpostApiClient\BpostException;
use Bpost\BpostApiClient\Common\BasicAttribute\PhoneNumber;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;

class PhoneNumberTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $value = str_repeat('a', 20);
        try {
            $test = new PhoneNumber($value);
            $this->assertEquals($value, $test->getValue());
        } catch (BpostException $ex) {
            $this->fail('Exception launched for valid value: "' . $value . '"');
        }

        $value = str_repeat('a', 21);
        try {
            new PhoneNumber($value);
            $this->fail('Exception uncaught for invalid value: "' . $value . '"');
        } catch (BpostInvalidLengthException $ex) {
            $this->assertTrue(true);
        }
    }

}
