<?php
namespace Bpost;

use TijsVerkoyen\Bpost\BpostException;
use TijsVerkoyen\Bpost\Common\ValidatedValue\LabelFormat;
use TijsVerkoyen\Bpost\Exception\BpostLogicException\BpostInvalidValueException;

class LabelFormatTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $value = 'a4';
        try {
            $test = new LabelFormat($value);
            $this->assertEquals('A4', $test->getValue());
        } catch (BpostException $ex) {
            $this->fail('Exception launched for valid value: "' . $value . '"');
        }

        $value = 'A4';
        try {
            $test = new LabelFormat($value);
            $this->assertEquals($value, $test->getValue());
        } catch (BpostException $ex) {
            $this->fail('Exception launched for valid value: "' . $value . '"');
        }

        $value = 'A5';
        try {
            new LabelFormat($value);
            $this->fail('Exception uncaught for invalid value: "' . $value . '"');
        } catch (BpostInvalidValueException $ex) {
            $this->assertTrue(true);
        }
    }
}
