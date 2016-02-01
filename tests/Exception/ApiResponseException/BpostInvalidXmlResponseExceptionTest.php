<?php

namespace TijsVerkoyen\Bpost\Geo6\test\Exception\ApiResponseException;

use TijsVerkoyen\Bpost\Exception\ApiResponseException\BpostInvalidXmlResponseException;

class BpostInvalidXmlResponseExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $ex = new BpostInvalidXmlResponseException();
        $this->assertSame('Invalid XML-response', $ex->getMessage());

        $ex = new BpostInvalidXmlResponseException('Oops');
        $this->assertSame('Invalid XML-response: Oops', $ex->getMessage());
    }
}
