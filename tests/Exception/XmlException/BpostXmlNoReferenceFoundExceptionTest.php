<?php

namespace TijsVerkoyen\Bpost\test\Exception\XmlException;

use TijsVerkoyen\Bpost\Exception\XmlException\BpostXmlNoReferenceFoundException;

class BpostXmlNoReferenceFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $ex = new BpostXmlNoReferenceFoundException();
        $this->assertSame('No reference found', $ex->getMessage());

        $ex = new BpostXmlNoReferenceFoundException('Oops');
        $this->assertSame('No reference found: Oops', $ex->getMessage());
    }
}
