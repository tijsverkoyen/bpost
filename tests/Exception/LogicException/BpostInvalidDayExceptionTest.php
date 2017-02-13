<?php

namespace Bpost\BpostApiClient\test\Exception\LogicException;

use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidDayException;

class BpostInvalidDayExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $ex = new BpostInvalidDayException('unicorn', array('Monday', 'Tuesday'));
        $this->assertSame('Invalid value (unicorn) for day, possible values are: Monday, Tuesday.', $ex->getMessage());
    }
}
