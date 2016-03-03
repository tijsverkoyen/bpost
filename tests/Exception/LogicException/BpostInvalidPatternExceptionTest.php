<?php

namespace TijsVerkoyen\Bpost\test\Exception\BpostLogicException;

use TijsVerkoyen\Bpost\Exception\BpostLogicException\BpostInvalidPatternException;

class BpostInvalidPatternExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testGetMessage()
    {
        $ex = new BpostInvalidPatternException('error', 'OOPS', '([A-Z]{3})');
        $this->assertSame('Invalid value (OOPS) for entry "error", pattern is: "([A-Z]{3})".', $ex->getMessage());
    }
}
