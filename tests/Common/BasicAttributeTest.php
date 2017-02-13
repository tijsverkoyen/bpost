<?php

use Bpost\BpostApiClient\Common\BasicAttribute;
use Bpost\BpostApiClient\Exception\BpostLogicException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidPatternException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;

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

        $this->setExpectedException('Bpost\BpostApiClient\Exception\BpostLogicException');
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

    public function testValidateChoice()
    {
        $fake = new BasicAttributeFake('qsd');
        try {
            $fake->validateChoice(array('aze', 'qsd'));
            $this->assertTrue(true);
        } catch (BpostInvalidValueException $e) {
            $this->fail('Exception launched for valid value: "qsd" (tested with ["aze", "qsd"])');
        }

        try {
            $fake->validateChoice(array('aze', 'wxc'));
            $this->fail('Exception uncaught for invalid value: "qsd" (tested with ["aze", "wxc"])');
        } catch (BpostInvalidValueException $e) {
            $this->assertTrue(true);
        }
    }

    public function testValidatePattern()
    {
        $fake = new BasicAttributeFake('pomme2016@antidot.com');
        try {
            $fake->validatePattern('([a-zA-Z0-9_\.\-+])+@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+');
            $this->assertTrue(true);
        } catch (BpostInvalidPatternException $e) {
            $this->fail('Exception launched for valid value: "pomme@antidot.com"');
        }

        try {
            $fake->validatePattern('([a-zA-Z0-9_\.\-+])+(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+');
            $this->fail('Exception uncaught for invalid value: "pomme2016@antidot.com"');
        } catch (BpostInvalidPatternException $e) {
            $this->assertTrue(true);
        }
    }
}
