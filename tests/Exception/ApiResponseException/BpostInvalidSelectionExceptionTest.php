<?php

namespace TijsVerkoyen\Bpost\test\Exception\ApiResponseException;

use TijsVerkoyen\Bpost\Exception\ApiResponseException\BpostInvalidSelectionException;

class BpostInvalidSelectionExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $ex = new BpostInvalidSelectionException('Oops');
        $this->assertSame('Oops', $ex->getMessage());

        $ex = new BpostInvalidSelectionException();
        $this->assertSame('', $ex->getMessage());
    }
}
