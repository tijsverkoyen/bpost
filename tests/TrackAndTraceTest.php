<?php

namespace TijsVerkoyen\Bpost\TrackAndTrace\test;

require_once __DIR__ . '/../../../autoload.php';
require_once 'config.php';

use TijsVerkoyen\Bpost\TrackAndTrace;

class TrackAndTraceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TrackAndTrace
     */
    private $trackAndTrace;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->trackAndTrace = new TrackAndTrace();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->trackAndTrace = null;
        parent::tearDown();
    }

    /**
     * Tests Geo6->getDeepLink()
     */
    public function testGetDeepLink()
    {
        $response = $this->trackAndTrace->getDeepLink(
            array('barcode1')
        );

        $this->assertEquals(
            'http://track.bpost.be/etr/light/performSearch.do?searchByItemCode=true&itemCodes=barcode1&oss_language=nl',
            $response
        );
        $this->assertEquals(
            'http://track.bpost.be/etr/light/performSearch.do?searchByItemCode=true&itemCodes=barcode1&oss_language=nl',
            $response
        );

        $response = $this->trackAndTrace->getDeepLink(
            array('barcode1'),
            'nl',
            'passphrase'
        );
        $this->assertEquals(
            'http://track.bpost.be/etr/light/performSearch.do?searchByItemCode=true&itemCodes=barcode1&oss_language=nl&checksum=e090e539871938788f853fd11cb4c8f9ef7c1886211b107119655801e7d4c6ee',
            $response
        );
    }
}
