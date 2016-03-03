<?php

use TijsVerkoyen\Bpost\BpostException;
use TijsVerkoyen\Bpost\Common\BasicAttribute\Language;
use TijsVerkoyen\Bpost\Exception\BpostLogicException\BpostInvalidValueException;

class LanguageTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $value = 'FR';
        try {
            $test = new Language($value);
            $this->assertEquals($value, $test->getValue());
        } catch (BpostException $ex) {
            $this->fail('Exception launched for valid value: "' . $value . '"');
        }

        $value = 'ES';
        try {
            new Language($value);
            $this->fail('Exception uncaught for invalid value: "' . $value . '"');
        } catch (BpostInvalidValueException $ex) {
            $this->assertTrue(true);
        }
    }

}
