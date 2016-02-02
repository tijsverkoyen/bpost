<?php

namespace TijsVerkoyen\Bpost\test\Exception\LogicException;

use TijsVerkoyen\Bpost\Exception\LogicException\BpostInvalidWeightException;

class BpostInvalidWeightExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $ex = new BpostInvalidWeightException(32, 30);
        $this->assertSame('Invalid weight (32 kg), maximum is 30.', $ex->getMessage());
    }
}
