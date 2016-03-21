<?php

namespace Bpost\BpostApiClient\test\Exception\XmlException;

use Bpost\BpostApiClient\Exception\XmlException\BpostXmlNoUserIdFoundException;

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
