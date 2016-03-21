<?php

namespace Bpost\BpostApiClient\test\Exception\ApiResponseException;

use Bpost\BpostApiClient\Exception\ApiResponseException\BpostTaxipostLocatorException;

class BpostTaxipostLocatorExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $ex = new BpostTaxipostLocatorException('Oops');
        $this->assertSame('Oops', $ex->getMessage());

        $ex = new BpostTaxipostLocatorException('');
        $this->assertSame('', $ex->getMessage());
    }
}
