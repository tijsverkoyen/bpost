<?php

namespace Bpost\BpostApiClient\test\Exception\BpostApiResponseException;

use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostInvalidSelectionException;

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
