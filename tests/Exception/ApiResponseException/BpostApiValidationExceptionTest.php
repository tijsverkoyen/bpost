<?php

namespace Bpost\BpostApiClient\test\Exception\ApiResponseException;

use Bpost\BpostApiClient\Exception\ApiResponseException\BpostApiValidationException;

class BpostApiValidationExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $ex = new BpostApiValidationException('Oops', 500);
        $this->assertSame('Oops', $ex->getMessage());
        $this->assertSame(500, $ex->getCode());

        $ex = new BpostApiValidationException('', 200);
        $this->assertSame('', $ex->getMessage());
        $this->assertSame(200, $ex->getCode());
    }
}
