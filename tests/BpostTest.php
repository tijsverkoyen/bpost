<?php

namespace TijsVerkoyen\Bpost\tests;

require_once __DIR__ . '/../../../autoload.php';
require_once 'config.php';

use \TijsVerkoyen\Bpost\Bpost;

/**
 * test case.
 */
class BpostTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Bpost
     */
    private $bpost;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->bpost = new Bpost(ACCOUNT_ID, PASSPHRASE);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->bpost = null;
        parent::tearDown();
    }

    /**
     * Tests Bpost->getTimeOut()
     */
    public function testGetTimeOut()
    {
        $this->bpost->setTimeOut(5);
        $this->assertEquals(5, $this->bpost->getTimeOut());
    }

    /**
     * Tests Bpost->getUserAgent()
     */
    public function testGetUserAgent()
    {
        $this->bpost->setUserAgent('testing/1.0.0');
        $this->assertEquals('PHP Bpost/' . Bpost::VERSION . ' testing/1.0.0', $this->bpost->getUserAgent());
    }
}
