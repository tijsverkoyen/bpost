<?php

namespace TijsVerkoyen\Bpost;

use TijsVerkoyen\Bpost\Exception\BpostLogicException;
use TijsVerkoyen\Bpost\Exception\BpostLogicException\BpostInvalidLengthException;

class BasicAttributeFake extends BasicAttribute
{

    /**
     * @return string
     */
    protected function getDefaultKey()
    {
        return 'defaultKey';
    }

    /**
     * @throws BpostLogicException
     */
    public function validate()
    {
        if ($this->getValue() === 'aze') {
            throw new BpostLogicException('aze');
        }
    }
}

class BasicAttributeTest extends \PHPUnit_Framework_TestCase
{

    public function testSetKey()
    {
        $fake = new BasicAttributeFake('qsd');
        $this->assertSame('defaultKey', $fake->getKey());

        $fake = new BasicAttributeFake('qsd', 'myKey');
        $this->assertSame('myKey', $fake->getKey());
    }

    public function testGetValue()
    {
        $fake = new BasicAttributeFake('qsd');
        $this->assertSame('qsd', $fake->getValue());

        $fake = new BasicAttributeFake('qsd');
        $this->assertSame('qsd', (string)$fake);

        $this->setExpectedException('TijsVerkoyen\Bpost\Exception\BpostLogicException');
        new BasicAttributeFake('aze');
    }

    public function testValidateLength()
    {
        $fake = new BasicAttributeFake('qsd');
        try {
            $fake->validateLength(10);
            $this->assertTrue(true);
        } catch (BpostInvalidLengthException $e) {
            $this->fail('Exception launched for valid value: "qsd" (tested with length=10)');
        }

        try {
            $fake->validateLength(2);
            $this->fail('Exception uncaught for invalid value: "qsd" (tested with length=2)');
        } catch (BpostInvalidLengthException $e) {
            $this->assertTrue(true);
        }

    }

}
