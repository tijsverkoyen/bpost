<?php

namespace Bpost\BpostApiClient\test\Exception\ApiResponseException;

use Bpost\BpostApiClient\Exception\ApiResponseException\BpostInvalidSelectionException;

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
