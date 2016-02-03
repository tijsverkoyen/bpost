<?php

namespace TijsVerkoyen\Bpost\test\Exception\ApiResponseException;

use TijsVerkoyen\Bpost\Exception\ApiResponseException\BpostTaxipostLocatorException;

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
