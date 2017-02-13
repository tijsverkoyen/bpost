<?php

namespace Bpost\BpostApiClient\test\Exception\BpostLogicException;

use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;

class BpostInvalidValueExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $ex = new BpostInvalidValueException('animals', 'beer', array('unicorn'));
        $this->assertSame('Invalid value (beer) for animals, possible values are: unicorn.', $ex->getMessage());

        $ex = new BpostInvalidValueException('animals', 'beer', array('unicorn', 'chicken'));
        $this->assertSame('Invalid value (beer) for animals, possible values are: unicorn, chicken.', $ex->getMessage());
    }
}
