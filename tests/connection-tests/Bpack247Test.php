<?php

namespace TijsVerkoyen\Bpost\Bpack247\test;

use TijsVerkoyen\Bpost\Bpack247;

class Bpack247Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Bpack247
     */
    private $bpack247;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->bpack247 = new Bpack247(BPACK_EMAIL, BPACK_PASSPHRASE);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->bpack247 = null;
        parent::tearDown();
    }

    /**
     * Tests Bpack247->getTimeOut()
     */
    public function testGetTimeOut()
    {
        $this->bpack247->setTimeOut(5);
        $this->assertSame(5, $this->bpack247->getTimeOut());
    }

    /**
     * Tests Bpack247->getUserAgent()
     */
    public function testGetUserAgent()
    {
        $this->bpack247->setUserAgent('testing/1.0.0');
        $this->assertSame(
            'PHP Bpost Bpack247/' . Bpack247::VERSION . ' testing/1.0.0',
            $this->bpack247->getUserAgent()
        );
    }

    /**
     * Tests Bpack247->getMember()
     */
    public function testGetMember()
    {
        $data = array(
            'id' => '344337728',
            'UserId' => '5f1f1b07-a8c4-4d4c-bd5b-cdace6cb7c84',
        );

        // @todo    create a member
        $response = $this->bpack247->getMember($data['id']);
        $this->assertSame($data['UserId'], $response->getUserID());
    }
}
