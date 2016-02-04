<?php

namespace TijsVerkoyen\Bpost\test\Exception\XmlException;

use TijsVerkoyen\Bpost\Exception\XmlException\BpostXmlInvalidItemException;

class BpostXmlInvalidItemExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $ex = new BpostXmlInvalidItemException();
        $this->assertSame('Invalid item', $ex->getMessage());

        $ex = new BpostXmlInvalidItemException('Oops');
        $this->assertSame('Invalid item: Oops', $ex->getMessage());
    }
}
