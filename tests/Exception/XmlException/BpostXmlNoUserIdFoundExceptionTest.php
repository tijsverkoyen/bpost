<?php

namespace TijsVerkoyen\Bpost\test\Exception\XmlException;

use TijsVerkoyen\Bpost\Exception\XmlException\BpostXmlNoUserIdFoundException;

class BpostXmlNoUserIdFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $ex = new BpostXmlNoUserIdFoundException();
        $this->assertSame('No UserId found', $ex->getMessage());

        $ex = new BpostXmlNoUserIdFoundException('Oops');
        $this->assertSame('No UserId found: Oops', $ex->getMessage());
    }
}
