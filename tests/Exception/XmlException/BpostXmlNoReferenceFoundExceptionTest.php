<?php

namespace Bpost\BpostApiClient\test\Exception\XmlException;

use Bpost\BpostApiClient\Exception\XmlException\BpostXmlNoReferenceFoundException;

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
