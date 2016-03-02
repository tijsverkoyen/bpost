<?php
namespace Bpost;

use TijsVerkoyen\Bpost\Bpost\Order\Box\National;

class NationalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the methods that are implemented by the children
     * But we would like to have full coverage... Stats-porn!
     */
    public function testMethodsThatAreImplementedByChildren()
    {
        $possibleProductValues = National::getPossibleProductValues();
        $this->assertInternalType('array', $possibleProductValues);
        $this->assertEmpty($possibleProductValues);
    }
}
