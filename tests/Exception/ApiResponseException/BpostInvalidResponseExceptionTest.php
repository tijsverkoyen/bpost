<?php

namespace Bpost\BpostApiClient\test\Exception\ApiResponseException;

use Bpost\BpostApiClient\Exception\ApiResponseException\BpostInvalidResponseException;

class BpostInvalidResponseExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $ex = new BpostInvalidResponseException('Oops');
        $this->assertSame('Invalid response: Oops', $ex->getMessage());

        $ex = new BpostInvalidResponseException();
        $this->assertSame('Invalid response', $ex->getMessage());
    }
}
