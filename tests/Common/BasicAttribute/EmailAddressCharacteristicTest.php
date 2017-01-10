<?php

use Bpost\BpostApiClient\BpostException;
use Bpost\BpostApiClient\Common\BasicAttribute\EmailAddressCharacteristic;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidPatternException;

class EmailAddressCharacteristicTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $value = 'pomme2016@antidot.com';
        try {
            $test = new EmailAddressCharacteristic($value);
            $this->assertEquals($value, $test->getValue());
        } catch (BpostException $ex) {
            $this->fail('Exception launched for valid value: "' . $value . '"');
        }

        $value = 'myBeautifulAndLongEmailAddressFor2016@antidot-company-based-at-Brussels.com';
        try {
            new EmailAddressCharacteristic($value);
            $this->fail('Exception uncaught for invalid value: "' . $value . '"');
        } catch (BpostInvalidLengthException $ex) {
            $this->assertTrue(true);
        }

        $value = 'pomme2016@antidot.c';
        try {
            new EmailAddressCharacteristic($value);
            $this->fail('Exception uncaught for invalid value: "' . $value . '"');
        } catch (BpostInvalidPatternException $ex) {
            $this->assertTrue(true);
        }
    }

}
